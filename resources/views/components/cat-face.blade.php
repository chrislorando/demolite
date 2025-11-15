@props(['hasErrors' => false])

<!-- Cat Face Animation Component -->
<div class="absolute inset-0 flex items-center justify-center pointer-events-none">
    <div class="flex flex-col items-center gap-6">
        <div id="catFace" class="relative w-32 h-40 rounded-3xl border-4 border-white flex flex-col items-center justify-center" data-has-errors="{{ $hasErrors ? 'true' : 'false' }}">
            <div class="absolute -top-3 left-4 w-0 h-0 border-l-4 border-r-4 border-b-8 border-l-transparent border-r-transparent border-b-white"></div>
            <div class="absolute -top-3 right-4 w-0 h-0 border-l-4 border-r-4 border-b-8 border-l-transparent border-r-transparent border-b-white"></div>

            <div class="flex gap-8 mb-6">
                <div class="relative w-10 h-12 bg-white rounded-full flex items-center justify-center">
                    <div class="eyes-pupil w-3 h-5 bg-neutral-900 rounded-full absolute" style="left:50%;top:50%;transform:translate(-50%,-50%);"></div>
                </div>
                <div class="relative w-10 h-12 bg-white rounded-full flex items-center justify-center">
                    <div class="eyes-pupil w-3 h-5 bg-neutral-900 rounded-full absolute" style="left:50%;top:50%;transform:translate(-50%,-50%);"></div>
                </div>
            </div>

            <div class="absolute left-0 top-1/2 flex flex-col gap-2">
                <div class="w-6 h-px bg-white"></div>
                <div class="w-6 h-px bg-white"></div>
                <div class="w-6 h-px bg-white"></div>
            </div>
            <div class="absolute right-0 top-1/2 flex flex-col gap-2">
                <div class="w-6 h-px bg-white"></div>
                <div class="w-6 h-px bg-white"></div>
                <div class="w-6 h-px bg-white"></div>
            </div>

            <div class="w-2 h-2 bg-white rounded-full mb-2 mt-2"></div>

            <svg id="catMouth" class="w-10 h-6 mt-1 transition-opacity duration-300" viewBox="0 0 100 50" xmlns="http://www.w3.org/2000/svg">
                <path d="M 50 30 Q 40 40 30 35" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" />
                <path d="M 50 30 Q 60 40 70 35" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" />
            </svg>
            <svg id="catSadMouth" class="w-10 h-6 mt-1 transition-opacity duration-300 opacity-0 absolute" viewBox="0 0 100 50" xmlns="http://www.w3.org/2000/svg">
                <path d="M 50 20 Q 40 10 30 15" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" />
                <path d="M 50 20 Q 60 10 70 15" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" />
            </svg>
        </div>

        <div id="meoowMessage" class="text-xl font-bold text-white transition-opacity duration-300 opacity-0">Meoow 😿</div>
    </div>
</div>

<script>
    (function() {
        const catFace = document.getElementById('catFace');
        if (!catFace) return;
        const catMouth = document.getElementById('catMouth');
        const catSadMouth = document.getElementById('catSadMouth');
        const meoowMessage = document.getElementById('meoowMessage');
        const hasErrors = catFace.dataset.hasErrors === 'true';
        let errorTimeout;

        function showSadCat() {
            catMouth.classList.add('opacity-0');
            catSadMouth.classList.remove('opacity-0');
            meoowMessage.classList.remove('opacity-0');
            clearTimeout(errorTimeout);
            errorTimeout = setTimeout(showHappyCat, 5000);
        }

        function showHappyCat() {
            catMouth.classList.remove('opacity-0');
            catSadMouth.classList.add('opacity-0');
            meoowMessage.classList.add('opacity-0');
            clearTimeout(errorTimeout);
        }

        if (hasErrors) {
            showSadCat();
        }

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', () => {
                setTimeout(() => {
                    if (catFace.dataset.hasErrors === 'true' || document.querySelector('[role="alert"]')) {
                        showSadCat();
                    } else {
                        showHappyCat();
                    }
                }, 100);
            });
        }

        document.addEventListener('mousemove', (e) => {
            const pupils = document.querySelectorAll('.eyes-pupil');
            const mouseX = e.clientX;
            const mouseY = e.clientY;
            pupils.forEach(pupil => {
                const eye = pupil.parentElement;
                const eyeRect = eye.getBoundingClientRect();
                const eyeCenterX = eyeRect.left + eyeRect.width / 2;
                const eyeCenterY = eyeRect.top + eyeRect.height / 2;
                const angle = Math.atan2(mouseY - eyeCenterY, mouseX - eyeCenterX);
                const distance = eyeRect.width / 2 - pupil.offsetWidth / 2 - 2;
                const pupilX = Math.cos(angle) * distance;
                const pupilY = Math.sin(angle) * distance;
                pupil.style.transform = `translate(calc(-50% + ${pupilX}px), calc(-50% + ${pupilY}px))`;
            });
        });
    })();
</script>