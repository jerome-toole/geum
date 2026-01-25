const animateItems = document.querySelectorAll('.animate');

const animateObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate--play');
            } else if (entry.boundingClientRect.top > 0) {
                // Only replay the animation if the user scrolls back up to the top
                entry.target.classList.remove('animate--play');
            }
        });
    },
    {
        rootMargin: '100% 0px -30px 0px',
    }
);

animateItems.forEach((item) => {
    animateObserver.observe(item);
});
