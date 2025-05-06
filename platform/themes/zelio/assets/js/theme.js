'use strict'

$(() => {
    function isRTL() {
        return $('body').attr('dir') === 'rtl'
    }

    const initTestimonials = () => {
        const slidersPerView = $('.slider-2').data('slides-per-view') || 2

        new Swiper('.slider-2', {
            slidesPerView: slidersPerView,
            spaceBetween: 30,
            slidesPerGroup: 1,
            centeredSlides: false,
            loop: true,
            autoplay: {
                delay: 4000,
            },
            breakpoints: {
                1200: {
                    slidesPerView: slidersPerView,
                },
                992: {
                    slidesPerView: slidersPerView,
                },
                768: {
                    slidesPerView: 2,
                },
                576: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
            pagination: {
                el: '.swiper-pagination',
            },
        })
    }

    const initCarouselScroll = () => {
        $('.carouselTicker-left').each(function () {
            $(this).carouselTicker({
                direction: 'prev',
                speed: 1,
                delay: 30,
            });
        });
        $('.carouselTicker-right').each(function () {
            $(this).carouselTicker({
                direction: 'next',
                speed: 1,
                delay: 30,
            });
        });
    }

    const initOdometerCounter = () => {
        const $element = $('.odometer')

        if ($element.length === 0) {
            return
        }

        $element.appear(function () {
            const odo = $('.odometer')
            odo.each(function () {
                const countNumber = $(this).data('count')
                $(this).html(countNumber)
            })
        })
    }

    const initDataBackground = () => {
        $('[data-background]').each(function () {
            $(this).css({
                'background-image': 'url(' + $(this).attr('data-background') + ')',
                'background-size': 'cover',
                'background-repeat': 'no-repeat',
            })
        })
    }

    const initMasonryFilter = () => {
        $('.masonry-active').imagesLoaded(function () {
            var $filter = '.masonry-active',
                $filterItem = '.filter-item',
                $filterMenu = '.filter-menu-active'
            if ($($filter).length > 0) {
                var $grid = $($filter).isotope({
                    itemSelector: $filterItem,
                    filter: '*',
                    masonry: {
                        // use outer width of grid-sizer for columnWidth
                        // columnWidth: 1,
                        columnWidth: '.grid-sizer',
                    },
                })
                // filter items on button click
                $($filterMenu).on('click', 'button', function () {
                    var filterValue = $(this).attr('data-filter')
                    $grid.isotope({
                        filter: filterValue,
                    })
                })
                // Menu Active Class
                $($filterMenu).on('click', 'button', function (event) {
                    event.preventDefault()
                    $(this).addClass('active')
                    $(this).siblings('.active').removeClass('active')
                })
            }
        })
    }

    const initCounter = () => {
        const counters = document.querySelectorAll('.counter')
        counters.forEach(function (counter) {
            const countTo = counter.getAttribute('data-count')
            let countNum = parseInt(counter.textContent)
            const duration = 4000
            const stepDuration = duration / Math.abs(countTo - countNum)
            const increment = countTo > countNum ? 1 : -1
            const timer = setInterval(function () {
                countNum += increment
                counter.textContent = countNum
                if (countNum == countTo) {
                    clearInterval(timer)
                }
            }, stepDuration)
        })
    }

    const initSwiper = () => {
        new Swiper('.slider-one', {
            slidesPerView: 2,
            spaceBetween: 20,
            slidesPerGroup: 1,
            centeredSlides: false,
            loop: true,
            autoplay: {
                delay: 4000,
            },
            breakpoints: {
                1200: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 2,
                },
                576: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        })
        new Swiper('.slider-two', {
            slidesPerView: 1,
            // spaceBetween: 20,
            slidesPerGroup: 1,
            centeredSlides: false,
            loop: true,
            autoplay: {
                delay: 4000,
            },
            pagination: {
                el: '.swiper-pagination',
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        })
        new Swiper('.slider-1', {
            slidesPerView: 3,
            spaceBetween: 20,
            slidesPerGroup: 1,
            centeredSlides: false,
            loop: true,
            autoplay: {
                delay: 4000,
            },
            breakpoints: {
                1200: {
                    slidesPerView: 3,
                },
                992: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 2,
                },
                576: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
            },
        })
    }

    const initCardScroll = () => {
        const { ScrollObserver, valueAtPercentage } = aat
        const cardsContainer = document.querySelector('.cards')
        const cards = document.querySelectorAll('.card-custom')

        if (!cardsContainer || !cards.length) {
            return
        }

        cardsContainer.style.setProperty('--cards-count', cards.length)
        cardsContainer.style.setProperty('--card-height', `${cards[0].clientHeight}px`)
        Array.from(cards).forEach((card, index) => {
            const offsetTop = 20 + index * 20
            card.style.paddingTop = `${offsetTop}px`
            if (index === cards.length - 1) {
                return
            }
            const toScale = 1 - (cards.length - 1 - index) * 0.1
            const nextCard = cards[index + 1]
            const cardInner = card.querySelector('.card__inner')
            ScrollObserver.Element(nextCard, {
                offsetTop,
                offsetBottom: window.innerHeight - card.clientHeight,
            }).onScroll(({ percentageY }) => {
                cardInner.style.scale = valueAtPercentage({
                    from: 1,
                    to: toScale,
                    percentage: percentageY,
                })
                cardInner.style.filter = `brightness(${valueAtPercentage({
                    from: 1,
                    to: 0.6,
                    percentage: percentageY,
                })})`
            })
        })
    }

    function initSlick(element, elementOptions = {}) {
        const $element = $(document).find(element)

        if (! $element.length) {
            return
        }

        let options = {
            slidesToShow: $element.data('items') ?? 5,
            slidesToScroll: 1,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 0,
            speed: 5000,
            cssEase: 'linear',
            pauseOnFocus: true,
            pauseOnHover: true,
            draggable: true,
            arrows: false,
            dots: false,
            loop: true,
            rtl: isRTL(),
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                    },
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3,
                    },
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                    },
                },
            ],
            ...elementOptions
        }

        $element.not('.slick-initialized').slick(options)
    }

    initSlick('.slick-slider')

    initTestimonials()
    initCarouselScroll()
    initOdometerCounter()
    initMasonryFilter()
    initCounter()
    initSwiper()
    initCardScroll()
    initDataBackground()

    document.addEventListener('shortcode.loaded', (e) => {
        const { name, html, attributes } = e.detail

        initDataBackground()

        if (name === 'testimonials') {
            initTestimonials()
        }

        initSlick('.slick-slider')

        if (['image-slider', 'hero-banner', 'skills', 'corporation'].includes(name)) {
            initCarouselScroll()
        }

        if (['skills', 'stats-counter'].includes(name)) {
            initOdometerCounter()
        }

        if (['services', 'projects'].includes(name)) {
            initImageRevealHover()
            initCardScroll()
        }

        if (name === 'projects') {
            initMasonryFilter()
        }

        if (name === 'stats-counter') {
            initCounter()
        }

        if (['projects', 'testimonials', 'blog-posts'].includes(name)) {
            initSwiper()
        }
    })
})
