const images = [
    '/images/ian-dooley-DJ7bWa-Gwks-unsplash.jpg',
    '/images/luca-bravo-9l_326FISzk-unsplash.jpg',
    '/images/olena-bohovyk-dIMJWLx1YbE-unsplash.jpg'
];

let current = 0;
let isOverlay1Visible = true;

const overlay1 = document.querySelector('.overlay1');
const overlay2 = document.querySelector('.overlay2');

// 画像を事前読み込み
function preloadImages(urls) {
    return Promise.all(
        urls.map(src => {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.src = src;
                img.onload = resolve;
                img.onerror = reject;
            });
        })
    );
}

// 背景クロスフェード切り替え
function changeBackground() {
    const nextImage = images[current % images.length];

    if (isOverlay1Visible) {
        overlay2.style.backgroundImage = `url('${nextImage}')`;
        overlay2.style.opacity = 1;
        overlay1.style.opacity = 0;
    } else {
        overlay1.style.backgroundImage = `url('${nextImage}')`;
        overlay1.style.opacity = 1;
        overlay2.style.opacity = 0;
    }

    isOverlay1Visible = !isOverlay1Visible;
    current++;
}

// 実行
window.addEventListener('load', async () => {
    await preloadImages(images);
    overlay1.style.backgroundImage = `url('${images[0]}')`;
    current = 1;
    setInterval(changeBackground, 5000);
});
