<x-layouts.app.full :title="__('Dashboard')">
    <div class="relative flex-1 w-full overflow-hidden bg-zinc-950 font-mono text-zinc-100" x-data="promptDefense()">
        {{-- Game Canvas --}}
        <canvas x-ref="canvas" class="absolute inset-0 w-full h-full block"></canvas>

        {{-- UI Overlay --}}
        <div class="absolute inset-0 pointer-events-none p-6 flex flex-col justify-between z-[1]">
            {{-- Header --}}
            <div class="flex justify-between items-start">
                <div>
                    <h1
                        class="text-2xl font-bold text-emerald-500 tracking-wider uppercase drop-shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                        Prompt Defense</h1>
                    <p class="text-xs text-emerald-500/70">System Integrity: <span x-text="health + '%'"></span></p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-white drop-shadow-md" x-text="score"></p>
                    <p class="text-xs text-zinc-400">TOKENS PROCESSED</p>
                </div>
            </div>

            {{-- Health Bar --}}
            <div class="w-full h-2 bg-zinc-900/50 rounded-full overflow-hidden border border-zinc-800">
                <div class="h-full bg-emerald-500 transition-all duration-300 ease-out shadow-[0_0_15px_rgba(16,185,129,0.8)]"
                    :style="'width: ' + health + '%'"></div>
            </div>
        </div>

        {{-- Start Screen --}}
        <div x-show="!isPlaying && !gameOver"
            class="absolute inset-0 z-[5] flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm transition-opacity"
            x-transition.opacity>
            <div class="text-center pointer-events-auto">
                <div class="mb-8 relative group cursor-pointer" @click="startGame()">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200">
                    </div>
                    <button
                        class="relative px-8 py-4 bg-zinc-900 rounded-lg leading-none flex items-center divide-x divide-zinc-600">
                        <span class="flex items-center space-x-5">
                            <span class="pr-6 text-emerald-400 font-bold text-xl tracking-widest">INITIALIZE
                                DEFENSE</span>
                        </span>
                        <span class="pl-6 text-emerald-600 group-hover:text-emerald-400 transition duration-200">
                            &rarr;
                        </span>
                    </button>
                </div>
                <p class="text-zinc-500 text-sm mt-4">Type the falling words to protect the core.</p>
            </div>
        </div>

        {{-- Game Over Screen --}}
        <div x-show="gameOver"
            class="absolute inset-0 z-[5] flex items-center justify-center bg-red-950/90 backdrop-blur-md transition-opacity"
            style="display: none;" x-transition.opacity>
            <div class="text-center pointer-events-auto">
                <h2
                    class="text-5xl font-bold text-red-500 mb-2 tracking-widest drop-shadow-[0_0_25px_rgba(239,68,68,0.6)]">
                    SYSTEM FAILURE</h2>
                <p class="text-xl text-red-200 mb-8">Final Score: <span x-text="score"></span></p>

                <button @click="startGame()"
                    class="px-8 py-3 bg-red-900/50 hover:bg-red-800/50 border border-red-500 text-red-100 rounded transition-all duration-200 hover:shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                    REBOOT SYSTEM
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('promptDefense', () => ({
                canvas: null,
                ctx: null,
                isPlaying: false,
                gameOver: false,
                score: 0,
                health: 100,
                words: [],
                particles: [],
                lastSpawn: 0,
                spawnRate: 2000,
                animationId: null,
                targetWord: null, // The word currently being typed

                // Word list related to AI/Tech
                wordList: [
                    'tensor', 'neuron', 'gradient', 'backprop', 'layer', 'bias', 'weight',
                    'epoch', 'batch', 'loss', 'accuracy', 'model', 'train', 'test',
                    'validation', 'overfit', 'underfit', 'dropout', 'relu', 'sigmoid',
                    'softmax', 'adam', 'sgd', 'momentum', 'convolution', 'pooling',
                    'padding', 'stride', 'kernel', 'filter', 'feature', 'map', 'vector',
                    'matrix', 'scalar', 'dimension', 'shape', 'reshape', 'flatten',
                    'dense', 'sparse', 'embedding', 'token', 'sequence', 'attention',
                    'transformer', 'encoder', 'decoder', 'head', 'mask', 'position',
                    'learning', 'rate', 'decay', 'schedule', 'optimizer', 'metric',
                    'confusion', 'precision', 'recall', 'f1', 'roc', 'auc', 'curve',
                    'regression', 'classification', 'clustering', 'kmeans', 'pca', 'tsne',
                    'autoencoder', 'gan', 'diffusion', 'prompt', 'completion', 'chat',
                    'gpt', 'bert', 'llama', 'stable', 'midjourney', 'dalle', 'whisper',
                    'vision', 'language', 'audio', 'multimodal', 'inference', 'deploy',
                    'server', 'cloud', 'edge', 'gpu', 'tpu', 'cuda', 'latency',
                    'throughput', 'parameter', 'hyperparameter', 'tuning', 'grid', 'random',
                    'bayesian', 'search', 'pipeline', 'preprocessing', 'normalization',
                    'augmentation', 'dataset', 'label', 'annotation', 'supervised',
                    'unsupervised', 'reinforcement', 'agent', 'environment', 'reward',
                    'policy', 'value', 'qlearning', 'deep', 'network', 'artificial',
                    'intelligence', 'machine', 'data', 'science', 'analytics', 'insight'
                ],

                init() {
                    this.canvas = this.$refs.canvas;
                    this.ctx = this.canvas.getContext('2d');
                    this.resize();
                    window.addEventListener('resize', () => this.resize());
                    window.addEventListener('keydown', (e) => this.handleInput(e));

                    // Initial render
                    this.render();
                },

                resize() {
                    this.canvas.width = this.canvas.offsetWidth;
                    this.canvas.height = this.canvas.offsetHeight;
                },

                startGame() {
                    this.isPlaying = true;
                    this.gameOver = false;
                    this.score = 0;
                    this.health = 100;
                    this.words = [];
                    this.particles = [];
                    this.targetWord = null;
                    this.spawnRate = 2000;
                    this.lastSpawn = performance.now();

                    if (this.animationId) cancelAnimationFrame(this.animationId);
                    this.loop();
                },

                spawnWord() {
                    const text = this.wordList[Math.floor(Math.random() * this.wordList.length)];
                    const x = Math.random() * (this.canvas.width - 100) + 50;
                    // Speed increases slightly with score
                    const speed = 0.5 + (this.score / 500);

                    this.words.push({
                        text: text,
                        originalText: text,
                        x: x,
                        y: -20,
                        speed: speed,
                        matchedIndex: 0, // How many chars matched
                        id: Math.random()
                    });
                },

                createExplosion(x, y, color = '#10b981') {
                    for (let i = 0; i < 15; i++) {
                        this.particles.push({
                            x: x,
                            y: y,
                            vx: (Math.random() - 0.5) * 10,
                            vy: (Math.random() - 0.5) * 10,
                            life: 1.0,
                            color: color
                        });
                    }
                },

                handleInput(e) {
                    if (!this.isPlaying || this.gameOver) return;

                    const char = e.key.toLowerCase();

                    // If we have a target word, check against it
                    if (this.targetWord) {
                        const nextChar = this.targetWord.text[this.targetWord.matchedIndex];
                        if (char === nextChar) {
                            this.targetWord.matchedIndex++;
                            if (this.targetWord.matchedIndex >= this.targetWord.text.length) {
                                // Word complete
                                this.score += this.targetWord.text.length * 10;
                                this.createExplosion(this.targetWord.x, this.targetWord.y);
                                this.words = this.words.filter(w => w.id !== this.targetWord.id);
                                this.targetWord = null;

                                // Increase difficulty
                                if (this.spawnRate > 500) this.spawnRate -= 20;
                            }
                        }
                    } else {
                        // Find a new target word starting with this char
                        // Prioritize words closest to bottom (highest y)
                        const candidates = this.words.filter(w => w.text.startsWith(char));
                        if (candidates.length > 0) {
                            // Sort by Y descending (closest to bottom first)
                            candidates.sort((a, b) => b.y - a.y);
                            this.targetWord = candidates[0];
                            this.targetWord.matchedIndex = 1; // First char matched

                            if (this.targetWord.matchedIndex >= this.targetWord.text.length) {
                                // Single letter word case (unlikely but possible)
                                this.score += 10;
                                this.createExplosion(this.targetWord.x, this.targetWord.y);
                                this.words = this.words.filter(w => w.id !== this.targetWord.id);
                                this.targetWord = null;
                            }
                        }
                    }
                },

                update(timestamp) {
                    if (!this.lastSpawn) this.lastSpawn = timestamp;

                    // Spawning
                    if (timestamp - this.lastSpawn > this.spawnRate) {
                        this.spawnWord();
                        this.lastSpawn = timestamp;
                    }

                    // Update words
                    for (let i = this.words.length - 1; i >= 0; i--) {
                        const word = this.words[i];
                        word.y += word.speed;

                        // Hit bottom
                        if (word.y > this.canvas.height) {
                            this.health -= 10;
                            this.createExplosion(word.x, word.y, '#ef4444');
                            this.words.splice(i, 1);

                            // Reset target if it was this word
                            if (this.targetWord && this.targetWord.id === word.id) {
                                this.targetWord = null;
                            }

                            if (this.health <= 0) {
                                this.gameOver = true;
                                this.isPlaying = false;
                            }
                        }
                    }

                    // Update particles
                    for (let i = this.particles.length - 1; i >= 0; i--) {
                        const p = this.particles[i];
                        p.x += p.vx;
                        p.y += p.vy;
                        p.life -= 0.05;
                        if (p.life <= 0) this.particles.splice(i, 1);
                    }
                },

                render() {
                    this.ctx.fillStyle = '#09090b'; // zinc-950
                    this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

                    // Draw grid effect
                    this.ctx.strokeStyle = '#18181b'; // zinc-900
                    this.ctx.lineWidth = 1;
                    for (let x = 0; x < this.canvas.width; x += 50) {
                        this.ctx.beginPath();
                        this.ctx.moveTo(x, 0);
                        this.ctx.lineTo(x, this.canvas.height);
                        this.ctx.stroke();
                    }
                    for (let y = 0; y < this.canvas.height; y += 50) {
                        this.ctx.beginPath();
                        this.ctx.moveTo(0, y);
                        this.ctx.lineTo(this.canvas.width, y);
                        this.ctx.stroke();
                    }

                    // Draw words
                    this.ctx.font = 'bold 20px monospace';
                    this.words.forEach(word => {
                        // Draw unmatched part
                        const unmatched = word.text.substring(word.matchedIndex);
                        const matched = word.text.substring(0, word.matchedIndex);

                        const totalWidth = this.ctx.measureText(word.text).width;
                        const matchedWidth = this.ctx.measureText(matched).width;

                        // Glow for active target
                        if (this.targetWord && this.targetWord.id === word.id) {
                            this.ctx.shadowBlur = 15;
                            this.ctx.shadowColor = '#10b981';
                        } else {
                            this.ctx.shadowBlur = 0;
                        }

                        // Draw matched part (Green)
                        this.ctx.fillStyle = '#10b981'; // emerald-500
                        this.ctx.fillText(matched, word.x - totalWidth / 2, word.y);

                        // Draw unmatched part (White/Gray)
                        this.ctx.fillStyle = '#e4e4e7'; // zinc-200
                        this.ctx.fillText(unmatched, word.x - totalWidth / 2 + matchedWidth, word.y);

                        this.ctx.shadowBlur = 0;
                    });

                    // Draw particles
                    this.particles.forEach(p => {
                        this.ctx.globalAlpha = p.life;
                        this.ctx.fillStyle = p.color;
                        this.ctx.beginPath();
                        this.ctx.arc(p.x, p.y, 3, 0, Math.PI * 2);
                        this.ctx.fill();
                        this.ctx.globalAlpha = 1.0;
                    });
                },

                loop(timestamp) {
                    if (!this.isPlaying) return;

                    this.update(timestamp);
                    this.render();

                    this.animationId = requestAnimationFrame((t) => this.loop(t));
                }
            }))
        })
    </script>
</x-layouts.app.full>