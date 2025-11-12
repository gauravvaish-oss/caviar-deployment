<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class StayInTouch extends AbstractWidget
{
    const NAME = 'vendor_stay_in_touch';

    public function getName()
    {
        return self::NAME;
    }

    public function getTitle()
    {
        return __('Newsletter Custom');
    }

    public function getIcon()
    {
        return 'fa fa-envelope';
    }

    public function getCategories()
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
            'default' => __('Stay In Touch'),
        ]);
        $this->addControl('subtitle', [
            'label' => __('Sub Title'),
            'type' => Controls::TEXT,
            'default' => __('Subscribe To Our Newsletter.'),
        ]);
         $this->addControl("icon", [
            'label' => __("Icon Image"),
            'type' => Controls::MEDIA,
        ]);

        $this->endControlsSection();
    }

    protected function contentTemplate()
    {
        ?>
        <div class="card-row ps-md-0">
            <div class="card rounded-0 text-center">
                <div class="card-body">
                    <h5 class="card-title">{{{settings.title}}}</h5>
                    <p class="card-text">{{{settings.subtitle}}}</p>

                    <form class="form subscribe"
                          novalidate
                          action=""
                          method="post"
                          data-mage-init='{"validation": {"errorClass": "mage-error"}}'
                          id="card-newsletter-form-editor">
                        <div class="input-group mt-md-4" style="display: flex !important;">
                            <input type="email"
                                   name="email"
                                   class="form-control rounded-0 your-email"
                                   placeholder="Your Email*"
                                   data-mage-init='{"mage/trim-input":{}}'
                                   data-validate="{required:true, 'validate-email':true}"
                                   required />
                            <button class="btn btn-warning rounded-0" type="submit">
                                <img src="{{{settings.icon.url}}}" alt="Subscribe Icon">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    protected function render(): string
    {
        $settings = $this->getSettingsForDisplay();
        $formId = 'card-newsletter-form-' . uniqid(); // unique id for multiple instances

        ob_start();
        ?>
        <div class="card-row ps-md-0">
            <div class="card rounded-0 text-center">
                <div class="card-body">
                    <h5 class="card-title"><?= $settings['title']; ?></h5>
                    <p class="card-text"><?= $settings['subtitle']; ?></p>

                    <form class="form subscribe"
                          novalidate
                          action="/newsletter/subscriber/new"
                          method="post"
                          data-mage-init='{"validation": {"errorClass": "mage-error"}}'
                          id="<?= $formId; ?>">
                        <div class="input-group mt-md-4" style="display: flex !important;">
                            <input type="email"
                                   name="email"
                                   class="form-control rounded-0 your-email"
                                   placeholder="<?= __('Your Email*'); ?>"
                                   data-mage-init='{"mage/trim-input":{}}'
                                   data-validate="{required:true, 'validate-email':true}"
                                   required />
                            <button class="btn btn-warning rounded-0" type="submit">
                                <img src="<?= $settings['icon']['url']; ?>" alt="Subscribe Icon">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script type="text/x-magento-init">
        {
            "*": {
                "Magento_Customer/js/block-submit-on-send": {
                    "formId": "<?= $formId; ?>"
                }
            }
        }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Helper function to get newsletter form action URL
     */
    private function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new');
    }
}
