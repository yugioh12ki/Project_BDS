// Xử lý dropdown menu
document.addEventListener('DOMContentLoaded', function() {
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownContent = document.getElementById('userDropdownContent');
    
    if (userDropdownBtn && userDropdownContent) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownContent.classList.toggle('show');
        });

        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!userDropdownBtn.contains(e.target)) {
                userDropdownContent.classList.remove('show');
            }
        });
    }
});

// Banner Slider
document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.banner-slider .slide');
    const dots = document.querySelectorAll('.slider-dots .dot');
    const totalSlides = slides.length;

    function showSlide(index) {
        // Ẩn tất cả slides và dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Hiển thị slide và dot hiện tại
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }

    // Xử lý nút prev/next
    const prevBtn = document.querySelector('.banner-nav.prev');
    const nextBtn = document.querySelector('.banner-nav.next');

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
        });

        nextBtn.addEventListener('click', () => {
            nextSlide();
        });
    }

    // Xử lý click vào dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });

    // Tự động chuyển slide
    const slideInterval = setInterval(nextSlide, 3000);

    // Dừng auto slide khi hover vào banner
    const bannerSlider = document.querySelector('.banner-slider');
    if (bannerSlider) {
        bannerSlider.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        bannerSlider.addEventListener('mouseleave', () => {
            setInterval(nextSlide, 3000);
        });
    }
}); 