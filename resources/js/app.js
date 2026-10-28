import './bootstrap';
import 'bootstrap';

// Initialisation globale du lazy-loader vidéo pour économie de data mobile
document.addEventListener('DOMContentLoaded', () => {
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
});
