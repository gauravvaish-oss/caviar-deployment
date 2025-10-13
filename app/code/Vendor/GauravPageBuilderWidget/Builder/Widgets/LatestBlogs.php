<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Mageplaza\Blog\Model\ResourceModel\Post\Collection;
use Mageplaza\Blog\Model\Api\BlogRepository;

class LatestBlogs extends AbstractWidget
{
    const NAME = 'vendor_custom_blogs';

    public function getName()
    {
        return self::NAME;
    }

    public function getTitle()
    {
        return __('Blogs Slider');
    }

    public function getIcon()
    {
        return 'fa fa-image';
    }

    public function getCategories()
    {
        return ['general'];
    }

       protected function registerControls()
{
    $blogRepo = ObjectManagerHelper::get(BlogRepository::class);
    $posts = $blogRepo->getAllPost();
    $options = [];
    foreach ($posts as $post) {
        $options[$post->getPostId()] = $post->getName();
    }

    $this->startControlsSection('content_section', [
        'label' => __('Blogs'),
        'tab'   => Controls::TAB_CONTENT,
    ]);

     $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Title Text'),
        ]);
    $this->addControl('blogs', [
        'label' => __('Select Blogs'),
        'type' => Controls::SELECT2,
        'multiple' => true,
        'options' => $options,
    ]);

    $this->endControlsSection();
}

    protected function contentTemplate()
{
    ?>
    <div class="row">
        <div class="main-title">
            <div class="d-flex align-items-center justify-content-center justify-content-md-between">
                <h2>{{{settings.title}}}</h2>
                <div class="d-none d-md-flex justify-content-end">
                    <div class="blog-prev swiper-button-prev" tabindex="0" role="button"></div>
                    <div class="blog-next swiper-button-next" tabindex="0" role="button"></div>
                </div>
            </div>
            <div class="swiper blog_slider">
                <div class="swiper-wrapper blog-slider-view-v" id="blog-slider-view"></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    require(['jquery', 'swiper'], function($, Swiper) {
        $(document).ready(function () {
            var blogIds = "{{{settings.blogs}}}";
            var blogsArray = blogIds ? blogIds.split(",") : [];
            var formKey = $('input[name="form_key"]').val();
            var $sliderWrapper = $(".blog-slider-view-v");

            $sliderWrapper.html("");

            var ajaxRequests = blogsArray.map(function(blogId) {
                blogId = blogId.trim();
                return $.ajax({
                    url: '/rest/V1/mpblog/post/view/' + blogId,
                    type: 'GET', // Use GET to fetch blog data
                    dataType: 'json',
                    headers: {
                        'Authorization': 'Bearer eyJraWQiOiIxIiwiYWxnIjoiSFMyNTYifQ.eyJ1aWQiOjEsInV0eXBpZCI6MiwiaWF0IjoxNzU5OTU2OTM3LCJleHAiOjE3NTk5NjA1Mzd9.9GxLeCYP4MrwsHwYFszL0BdKUH43eFih68sW87q6PfQ'
                    },
                    success: function(response) {
                        console.log(response)
                        if (response) {
                            var post = response;
                            var html = `
                                <div class="swiper-slide">
                                    <div class="blog_section_bg">
                                        <img src="/media/mageplaza/blog/post/${post.image || 'images/watching_TV.png'}" alt="${post.name}" class="img-fluid">
                                        <h6>${post.name}</h6>
                                    </div>
                                </div>
                            `;
                            $sliderWrapper.append(html);
                        } else {
                            console.error("Failed to load blog:", blogId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error for blog " + blogId + ":", xhr.responseText);
                    }
                });
            });

            $.when.apply($, ajaxRequests).done(function() {
                new Swiper('.blog_slider', {
                    slidesPerView: 3,
                    spaceBetween: 20,
                    loop: true,
                    navigation: {
                        nextEl: '.blog-next',
                        prevEl: '.blog-prev',
                    },
                    breakpoints: {
                        768: { slidesPerView: 2 },
                        480: { slidesPerView: 2 }
                    }
                });
            });
        });
    });
    </script>
    <?php
}

 protected function render(): string
{
        $settings = $this->getSettings();
        $blogsArray = isset($settings['blogs']) && is_array($settings['blogs'])
                        ? array_filter(array_map('trim', $settings['blogs']))
                        : [];
        $title = $settings['title'] ?? '';
        ob_start();
    ?>
    <div class="row">
        <div class="main-title">
            <div class="d-flex align-items-center justify-content-center justify-content-md-between">
                <h2><?= $title; ?></h2>
                <div class="d-none d-md-flex justify-content-end">
                    <div class="blog-prev swiper-button-prev" tabindex="0" role="button"></div>
                    <div class="blog-next swiper-button-next" tabindex="0" role="button"></div>
                </div>
            </div>
            <div class="swiper blog_slider">
                <div class="swiper-wrapper" id="blog-slider-view"></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    require(['jquery', 'swiper'], function($, Swiper) {
        $(document).ready(function () {
            var blogIds = <?= json_encode($blogsArray); ?>;
            var formKey = $('input[name="form_key"]').val();
            var $sliderWrapper = $(".blog-slider-view-v");

            $sliderWrapper.html("");

            var ajaxRequests = blogIds.map(function(blogId) {
                blogId = blogId.trim();
                return $.ajax({
                    url: '/rest/V1/mpblog/post/view/' + blogId,
                    type: 'GET', // Use GET to fetch blog data
                    dataType: 'json',
                    headers: {
                        'Authorization': 'Bearer eyJraWQiOiIxIiwiYWxnIjoiSFMyNTYifQ.eyJ1aWQiOjEsInV0eXBpZCI6MiwiaWF0IjoxNzU5OTU2OTM3LCJleHAiOjE3NTk5NjA1Mzd9.9GxLeCYP4MrwsHwYFszL0BdKUH43eFih68sW87q6PfQ'
                    },
                    success: function(response) {
                        console.log(response)
                        if (response) {
                            var post = response;
                            var html = `
                                <div class="swiper-slide">
                                    <div class="blog_section_bg">
                                        <img src="/media/mageplaza/blog/post/${post.image || 'images/watching_TV.png'}" alt="${post.name}" class="img-fluid">
                                        <h6>${post.name}</h6>
                                    </div>
                                </div>
                            `;
                            $sliderWrapper.append(html);
                        } else {
                            console.error("Failed to load blog:", blogId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error for blog " + blogId + ":", xhr.responseText);
                    }
                });
            });

            $.when.apply($, ajaxRequests).done(function() {
                new Swiper('.blog_slider', {
                    slidesPerView: 3,
                    spaceBetween: 20,
                    loop: true,
                    navigation: {
                        nextEl: '.blog-next',
                        prevEl: '.blog-prev',
                    },
                    breakpoints: {
                        768: { slidesPerView: 2 },
                        480: { slidesPerView: 2 }
                    }
                });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

}