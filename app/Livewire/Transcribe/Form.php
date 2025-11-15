<?php

namespace App\Livewire\Transcribe;

use App\Models\VoiceNote;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app.full')]
class Form extends Component
{
    use WithFileUploads;

    public $file;

    public string $title = '';

    public bool $isRecording = false;

    public ?string $recordedAudio = null;

    public string $liveTranscript = '';

    public string $formattedNotes = '';

    public string $status = 'idle';

    public int $duration = 0;

    public ?string $audioUrl = null;

    protected function rules(): array
    {
        return [
            'file' => 'required_without:recordedAudio|file|mimes:mp3,wav,m4a,ogg,webm|max:51200',
            'title' => 'required|string|max:255',
        ];
    }

    public function submit()
    {
        $this->validate();

        $id = (string) Str::uuid();

        // Handle recorded audio
        if ($this->recordedAudio) {
            // Decode base64 audio
            $audioData = explode(',', $this->recordedAudio);
            $decodedAudio = base64_decode($audioData[1]);

            // Generate filename
            $fileName = 'recording_'.time().'.webm';
            $path = 'voice-notes/'.$fileName;

            // Store to S3
            Storage::disk('s3')->put($path, $decodedAudio);
            $url = Storage::disk('s3')->url($path);

            VoiceNote::create([
                'id' => $id,
                'title' => $this->title,
                'file_name' => $fileName,
                'file_size' => strlen($decodedAudio),
                'file_url' => $url,
                'status' => 'created',
            ]);
        } else {
            // Handle uploaded file
            $path = $this->file->store('voice-notes', 's3');
            $url = Storage::disk('s3')->url($path);

            VoiceNote::create([
                'id' => $id,
                'title' => $this->title,
                'file_name' => $this->file->getClientOriginalName(),
                'file_size' => $this->file->getSize(),
                'file_url' => $url,
                'status' => 'created',
            ]);
        }

        $this->resetForm();

        return $this->redirect(route('transcribe.index'), navigate: true);
    }

    public function toggleRecording(): void
    {
        $this->isRecording = ! $this->isRecording;
    }

    public function clearRecording(): void
    {
        $this->recordedAudio = null;
        $this->isRecording = false;
        $this->liveTranscript = '';
        $this->formattedNotes = '';
        $this->status = 'idle';
        $this->audioUrl = null;
    }

    public function formatTranscript(string $rawTranscript, int $duration)
    {
        $this->status = 'formatting';
        $this->duration = $duration;

        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that formats voice transcripts into structured notes.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this transcript and extract:
                                        1. A clear summary (2-3 sentences or 1-2 paragraphs depending on length)
                                        2. Key points (as bullet points)
                                        3. Action items with due dates if mentioned (format: 'Action: [task] | Due: [date or 'Not specified']')

                                        Format the output clearly with headers.

                                        Transcript: {$rawTranscript}",
                    ],
                ],
            ]);

            $this->formattedNotes = $response->choices[0]->message->content;
            $this->status = 'completed';

            return $this->formattedNotes;
        } catch (\Exception $e) {
            $this->status = 'error';
            $this->formattedNotes = 'Error formatting transcript: '.$e->getMessage();

            return null;
        }
    }

    public function saveVoiceNote()
    {
        // Allow saving if at least one is present (audio or transcript)
        if (! $this->recordedAudio && ! $this->liveTranscript) {
            $this->dispatch('error', message: 'No recording or transcript available');

            return;
        }

        try {
            $id = (string) Str::uuid();

            // Decode and save audio
            $audioData = explode(',', $this->recordedAudio);
            $decodedAudio = base64_decode($audioData[1]);

            $fileName = 'recording_'.time().'.webm';
            $path = 'voice-notes/'.$fileName;

            Storage::disk('s3')->put($path, $decodedAudio);
            $url = Storage::disk('s3')->url($path);

            // Generate title from transcript
            $title = $this->title ?: $this->generateTitle($this->liveTranscript);

            // Create voice note
            $voiceNote = \App\Models\VoiceNote::create([
                'id' => $id,
                'title' => $title,
                'file_name' => $fileName,
                'file_size' => strlen($decodedAudio),
                'file_url' => $url,
                'transcript' => $this->liveTranscript,
                'response' => $this->formattedNotes,
                'duration' => $this->duration,
                'status' => 'completed',
            ]);

            // Extract and save action items
            $this->extractAndSaveActionItems($voiceNote->id, $this->formattedNotes);

            $this->dispatch('success', message: 'Voice note saved successfully!');
            $this->resetForm();

            return $this->redirect(route('transcribe.index'), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to save: '.$e->getMessage());
        }
    }

    private function generateTitle(string $transcript): string
    {
        $words = explode(' ', $transcript);
        $title = implode(' ', array_slice($words, 0, 6));

        return strlen($title) > 50 ? substr($title, 0, 47).'...' : $title;
    }

    private function extractAndSaveActionItems(string $voiceNoteId, string $formattedNotes): void
    {
        // Extract action items using regex
        $pattern = '/Action:\s*(.+?)\s*\|\s*Due:\s*(.+?)(?=\n|$)/i';
        preg_match_all($pattern, $formattedNotes, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $description = trim($match[1]);
            $dueDate = trim($match[2]);

            // Parse due date
            $parsedDate = null;
            if (strtolower($dueDate) !== 'not specified') {
                try {
                    $parsedDate = \Carbon\Carbon::parse($dueDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    // If parsing fails, leave as null
                }
            }

            \App\Models\VoiceNoteItem::create([
                'voice_note_id' => $voiceNoteId,
                'description' => $description,
                'status' => 'todo',
                'due_date' => $parsedDate,
            ]);
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'file',
            'title',
            'recordedAudio',
            'isRecording',
            'liveTranscript',
            'formattedNotes',
            'status',
            'duration',
            'audioUrl',
        ]);
    }

    public function cancel(): void
    {
        $this->redirect(route('transcribe.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.transcribe.form');
    }
}
