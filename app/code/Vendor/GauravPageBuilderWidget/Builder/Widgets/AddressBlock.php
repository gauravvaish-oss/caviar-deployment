<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class AddressBlock extends AbstractWidget
{
     const NAME = 'vendor_custom_address_block';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Address');
    }

    public function getIcon(): string
    {
        return 'fa fa-image';
    }

    public function getCategories(): array
    {
        return ['general'];
    }

     protected function registerControls()
    {
       $this->startControlsSection('content_section', [
            'label' => __('Content'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Title Text'),
        ]);
        $this->addControl('address', [
            'label' => __('Address'),
            'type' => Controls::TEXTAREA,
            'default' => __('Unit 1 & 2, 4th Floor, Tower B, Vatika Mindscapes, Mathura Road, Faridabad 121003, Delhi NCR'),
        ]);
         $this->addControl('mobile', [
            'label' => __('Mobile'),
            'type' => Controls::TEXTAREA,
            'default' => __('0123456789'),
        ]);
        $this->addControl('email', [
            'label' => __('Email'),
            'type' => Controls::TEXTAREA,
            'default' => __('admin@example.com'),
        ]);
        $this->addControl("banner_img", [
            'label' => __("Banner Image"),
            'type'  => Controls::MEDIA,
        ]);

        $this->endControlsSection();
    }

    protected function contentTemplate(){
        ?>
        <div class="caviarcontainer ">
            <div class="caviar-part">
                <h3 class="caviar">{{{settings.title}}}</h3>
                <p style="align-items: flex-start;"><img style="margin-top: 2px;" src="./images/contact-location.png" alt="Location Icon" class="me-2">{{{settings.address}}}</p>
                <p><img src="./images/contact-phone.png" alt="Phone Icon" class="me-2">
                                        <a href="tel:+91 {{{settings.mobile}}}">+91 {{{settings.mobile}}}</a>
                </p>
                <p><img src="./images/contact-email.png" alt="Mail Icon" class="me-2">
                                        <a href="mailto:{{{settings.email}}}">{{{settings.email}}}</a>
                </p>
            </div>
                        <img src="{{{settings.banner_img.url}}}" alt="" class="img-fluid  w-full caviar-img">
        </div>
        <?php
    }

    protected function render(): string
    { 
        $settings = $this->getSettings();
        
        ob_start();
        ?>
        <div class="caviarcontainer ">
            <div class="caviar-part">
                <h3 class="caviar"><?= $settings['title']; ?></h3>
                <p style="align-items: flex-start;"><img style="margin-top: 2px;" src="./images/contact-location.png" alt="Location Icon" class="me-2"><?= $settings['address']; ?></p>
                <p><img src="./images/contact-phone.png" alt="Phone Icon" class="me-2">
                                        <a href="tel:+91 <?= $settings['mobile']; ?>">+91 <?= $settings['mobile']; ?></a>
                </p>
                <p><img src="./images/contact-email.png" alt="Mail Icon" class="me-2">
                                        <a href="mailto:<?= $settings['email']; ?>"><?= $settings['email']; ?></a>
                </p>
            </div>
                        <img src="<?= $settings['banner_img']['url']; ?>" alt="" class="img-fluid  w-full caviar-img">
        </div>
        <?php
            return ob_get_clean();

    }

}