<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class PrivacyPolicyTabs extends AbstractWidget
{
    const NAME = 'vendor_custom_privacy_policy_tabs';

    public function getName()
    {
        return self::NAME;
    }

    public function getTitle()
    {
        return __('Privacy Policy Tabs');
    }

    public function getIcon()
    {
        return 'fa fa-sliders';
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
        
         $this->addControl('main_title', [
                'label' => __('Tab Title'),
                'type' => Controls::TEXT,
                'default' => __('Limited Time Offer'),
            ]);
        // Allow up to 6 logos
        for ($i = 1; $i <= 5; $i++) {

            $this->addControl('privacy_title_'.$i, [
                'label' => __("Tab Title $i"),
                'type' => Controls::TEXT,
                'default' => __('Limited Time Offer'),
            ]);
            $this->addControl('privacy_tab_image_'.$i, [
                'label' => __("Image $i"),
                'type'  => Controls::MEDIA,
            ]);
            $this->addControl(
            'privacy_description_'.$i,
            [
                'label' => __("Tab Description $i"),
                'type' => Controls::TEXTAREA,
                'placeholder' => __('Enter your description'),
                'default' => __('I am a description. Click the edit button to change this text.'),
                'separator' => 'none',
                'show_label' => false,
            ]
            );
        }

        $this->endControlsSection();
    }

   
protected function contentTemplate()
    {
        ?>
            <section class="policy-wrapper container">
                <div class="row g-4">
                    <!-- Sidebar -->
                    <aside class="col-lg-2">
                        <div class="sidebar privacy-sidebar">
                            <div class="sidebar-title">{{{settings.main_title}}}</div>

                            <ul class="nav nav-pills privacy-nav_pills flex-column gap-3" id="policy-tabs" role="tablist" aria-orientation="vertical">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link privacy-nav_link" id="tab-collect" data-bs-toggle="pill" data-bs-target="#pane-collect" type="button" role="tab" aria-controls="pane-collect" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <!-- inline svg icon -->
                                            <img class="policy-vector" src="{{{settings.privacy_tab_image_1.url}}}" alt="policy img">

                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label">{{{settings.privacy_title_1}}}</span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-cookies" data-bs-toggle="pill" data-bs-target="#pane-cookies" type="button" role="tab" aria-controls="pane-cookies" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="{{{settings.privacy_tab_image_2.url}}}" alt="privacy img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label">{{{settings.privacy_title_2}}}</span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-usage" data-bs-toggle="pill" data-bs-target="#pane-usage" type="button" role="tab" aria-controls="pane-usage" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="{{{settings.privacy_tab_image_3.url}}}" alt="usage img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label">{{{settings.privacy_title_2}}}</span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-rights" data-bs-toggle="pill" data-bs-target="#pane-rights" type="button" role="tab" aria-controls="pane-rights" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="{{{settings.privacy_tab_image_4.url}}}" alt="rights img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label">{{{settings.privacy_title_4}}}</span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-sharing" data-bs-toggle="pill" data-bs-target="#pane-sharing" type="button" role="tab" aria-controls="pane-sharing" aria-selected="true">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="{{{settings.privacy_tab_image_5.url}}}" alt="sharing img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label">{{{settings.privacy_title_5}}}</span>
                                        </div>
                                    </button>
                                </li>
                            </ul>


                        </div>
                    </aside>

                    <!-- Content -->
                    <div class="col-lg-10">
                        <div class="tab-content policy" id="policy-tabContent">
                            <!-- Collect -->
                            <div class="tab-pane fade" id="pane-collect" role="tabpanel" aria-labelledby="tab-collect" tabindex="0">
                                {{{settings.privacy_description_1}}}
                            </div>

                            <!-- Cookies -->
                            <div class="tab-pane fade" id="pane-cookies" role="tabpanel" aria-labelledby="tab-cookies" tabindex="0">
                                {{{settings.privacy_description_2}}}
                            </div>

                            <!-- Usage -->
                            <div class="tab-pane fade" id="pane-usage" role="tabpanel" aria-labelledby="tab-usage" tabindex="0">
                                {{{settings.privacy_description_3}}}
                            </div>

                            <!-- Rights -->
                            <div class="tab-pane fade" id="pane-rights" role="tabpanel" aria-labelledby="tab-rights" tabindex="0">
                                {{{settings.privacy_description_4}}}
                            </div>

                            <!-- Sharing -->
                            <div class="tab-pane fade active show" id="pane-sharing" role="tabpanel" aria-labelledby="tab-sharing" tabindex="0">
                                {{{settings.privacy_description_5}}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }
    protected function render(): string
    {
        $settings = $this->getSettingsForDisplay();
        $main_title = $settings['main_title'] ?? "Title Text";
        $title_1 = $settings['privacy_title_1'] ?? "Title Text";
        $title_2 = $settings['privacy_title_2'] ?? "Title Text";
        $title_3 = $settings['privacy_title_3'] ?? "Title Text";
        $title_4 = $settings['privacy_title_4'] ?? "Title Text";
        $title_5 = $settings['privacy_title_5'] ?? "Title Text";
        $description_1 = $settings['privacy_description_1'] ?? "Description Text";
        $description_2 = $settings['privacy_description_2'] ?? "Description Text";
        $description_3 = $settings['privacy_description_3'] ?? "Description Text";
        $description_4 = $settings['privacy_description_4'] ?? "Description Text";
        $description_5 = $settings['privacy_description_5'] ?? "Description Text";
        $tab_image_1 = $settings['privacy_tab_image_1']['url'] ?? "tab_image Text";
        $tab_image_2 = $settings['privacy_tab_image_2']['url'] ?? "tab_image Text";
        $tab_image_3 = $settings['privacy_tab_image_3']['url'] ?? "tab_image Text";
        $tab_image_4 = $settings['privacy_tab_image_4']['url'] ?? "tab_image Text";
        $tab_image_5 = $settings['privacy_tab_image_5']['url'] ?? "tab_image Text";

// dd($settings);die;
        ob_start();
        ?>
        <section class="policy-wrapper container">
                <div class="row g-4">
                    <!-- Sidebar -->
                    <aside class="col-lg-2">
                        <div class="sidebar privacy-sidebar">
                            <div class="sidebar-title"><?= $main_title;?></div>

                            <ul class="nav nav-pills privacy-nav_pills flex-column gap-3" id="policy-tabs" role="tablist" aria-orientation="vertical">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link privacy-nav_link" id="tab-collect" data-bs-toggle="pill" data-bs-target="#pane-collect" type="button" role="tab" aria-controls="pane-collect" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <!-- inline svg icon -->
                                            <img class="policy-vector" src="<?= $tab_image_1;?>" alt="policy img">

                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label"><?= $title_1;?></span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-cookies" data-bs-toggle="pill" data-bs-target="#pane-cookies" type="button" role="tab" aria-controls="pane-cookies" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="<?= $tab_image_2;?>" alt="privacy img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label"><?= $title_2;?></span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-usage" data-bs-toggle="pill" data-bs-target="#pane-usage" type="button" role="tab" aria-controls="pane-usage" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="<?= $tab_image_3;?>" alt="usage img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label"><?= $title_3;?>}</span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-rights" data-bs-toggle="pill" data-bs-target="#pane-rights" type="button" role="tab" aria-controls="pane-rights" aria-selected="false" tabindex="-1">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="<?= $tab_image_4;?>" alt="rights img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label"><?= $title_4;?></span>
                                        </div>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-sharing" data-bs-toggle="pill" data-bs-target="#pane-sharing" type="button" role="tab" aria-controls="pane-sharing" aria-selected="true">
                                        <span class="pill-icon" aria-hidden="true">
                                            <img class="policy-vector" src="<?= $tab_image_5;?>" alt="sharing img">
                                        </span>
                                        <div class="tab_padd">
                                            <span class="pill-label"><?= $title_5;?></span>
                                        </div>
                                    </button>
                                </li>
                            </ul>


                        </div>
                    </aside>

                    <!-- Content -->
                    <div class="col-lg-10">
                        <div class="tab-content policy" id="policy-tabContent">
                            <!-- Collect -->
                            <div class="tab-pane fade" id="pane-collect" role="tabpanel" aria-labelledby="tab-collect" tabindex="0">
                                <?= $description_1;?>
                            </div>

                            <!-- Cookies -->
                            <div class="tab-pane fade" id="pane-cookies" role="tabpanel" aria-labelledby="tab-cookies" tabindex="0">
                                <?= $description_2;?>
                            </div>

                            <!-- Usage -->
                            <div class="tab-pane fade" id="pane-usage" role="tabpanel" aria-labelledby="tab-usage" tabindex="0">
                                <?= $description_3;?>
                            </div>

                            <!-- Rights -->
                            <div class="tab-pane fade" id="pane-rights" role="tabpanel" aria-labelledby="tab-rights" tabindex="0">
                                <?= $description_4;?>
                            </div>

                            <!-- Sharing -->
                            <div class="tab-pane fade active show" id="pane-sharing" role="tabpanel" aria-labelledby="tab-sharing" tabindex="0">
                                <?= $description_5;?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php
        return ob_get_clean();
    }
}
