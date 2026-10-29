import './bootstrap';
import 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialisation du lazy-loader vidéo YouTube (économie de data mobile)
    document.querySelectorAll('.video-facade').forEach(facade => {
        facade.addEventListener('click', function () {
            const youtubeId = this.dataset.youtubeId;
            if (!youtubeId) return;

            const iframe = document.createElement('iframe');
            iframe.setAttribute('src', `https://www.youtube-nocookie.com/embed/${youtubeId}?autoplay=1&rel=0`);
            iframe.setAttribute('title', this.dataset.title || 'Vidéo de cours');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
            iframe.setAttribute('allowfullscreen', 'true');

            this.innerHTML = '';
            this.appendChild(iframe);
        });
    });

    // 2. Animation gestuelle d'écriture du slogan Educ'Academy
    initSloganGesture();
});

/**
 * Animation d'écriture gestuelle progressive (effet machine à écrire vivante avec curseur)
 */
function initSloganGesture() {
    const sloganTypewriters = document.querySelectorAll('.ea-slogan-typewriter');
    if (!sloganTypewriters.length) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    sloganTypewriters.forEach(el => {
        const fullText = el.getAttribute('data-slogan') || el.textContent.trim();
        const parentBox = el.closest('.ea-slogan-login-box, .ea-slogan-banner');
        const cursor = parentBox ? parentBox.querySelector('.ea-slogan-cursor') : null;

        if (prefersReducedMotion) {
            el.textContent = fullText;
            if (parentBox) parentBox.classList.add('slogan-box-active');
            return;
        }

        el.textContent = '';
        let charIndex = 0;

        function typeNextChar() {
            if (charIndex < fullText.length) {
                el.textContent += fullText.charAt(charIndex);
                charIndex++;
                // Légère variation aléatoire pour imiter le geste humain
                const isSpace = fullText.charAt(charIndex - 1) === ' ';
                const speed = isSpace ? 80 : 38 + Math.floor(Math.random() * 25);
                setTimeout(typeNextChar, speed);
            } else {
                if (cursor) cursor.classList.add('cursor-idle');
                if (parentBox) parentBox.classList.add('slogan-box-active');
            }
        }

        // Lancement après 250ms pour laisser la page s'afficher
        setTimeout(typeNextChar, 250);
    });
}
