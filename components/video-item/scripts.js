class VideoItem {
    constructor(videoItem) {
        this.videoItem = videoItem;
        this.play = this.videoItem.querySelector('.js-video-item-play');
        this.close = this.videoItem.querySelector('.video-item__video-close');
        this.video = this.videoItem.querySelector('.video-item__video');
        this.iframe = this.videoItem.querySelector('iframe');

        this.init();
    }

    init() {
        this.play.addEventListener('click', () => {
            this.playVideo();
        });

        this.video.addEventListener('click', ({ target }) => {
            if (target !== this.iframe) {
                this.closeVideo();
            }
        });

        document.addEventListener('keydown', ({ key }) => {
            if (key === 'Escape' && this.isVideoPlaying()) {
                this.closeVideo();
            }
        });
    }

    playVideo() {
        this.videoItem.classList.add('video-item--play');
        this.iframe.src = this.iframe.getAttribute('data-src');
        document.documentElement.classList.add('no-scroll');
    }

    closeVideo() {
        this.videoItem.classList.remove('video-item--play');
        this.iframe.src = '';
        document.documentElement.classList.remove('no-scroll');
    }

    isVideoPlaying() {
        return this.iframe.src !== '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const videoItems = document.querySelectorAll('.video-item');

    videoItems.forEach((element) => {
        new VideoItem(element);
    });
});
