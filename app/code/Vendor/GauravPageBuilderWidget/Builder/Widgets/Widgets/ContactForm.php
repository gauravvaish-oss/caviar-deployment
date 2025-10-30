<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class ContactForm extends AbstractWidget
{
     const NAME = 'vendor_custom_contact_form';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Contact Form');
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
            'label' => __('Form Title'),
            'type' => Controls::TEXT,
            'default' => __('Get In Touch'),
        ]);

        $this->endControlsSection();
    }

   protected function contentTemplate()
{
    ?>
    <div class="git">
        <div class="get-in-touch main-title">
            <h3 class="getintouch">{{{settings.title}}}</h3>

            <form id="ajax-contact-form">
                <div class="row">
                    <div class="col-md-6 p-2">
                        <input type="text" name="first_name" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="First Name*" required>
                    </div>
                    <div class="col-md-6 p-2">
                        <input type="text" name="last_name" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Last Name*" required>
                    </div>
                    <div class="col-md-6 p-2">
                        <input type="email" name="email" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Email*" required>
                    </div>
                    <div class="col-md-6 p-2">
                        <input type="text" name="telephone" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Phone*" required>
                    </div>
                    <div class="col-md-12 p-2">
                        <textarea name="comment" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Do You Have Any Additional Info?" rows="4" required></textarea>
                    </div>
                    <input type="hidden" name="hideit" value="">
                    <div class="col-md-12 p-2">
                        <button type="submit" class="submit-button mb-3 p-3">Submit</button>
                    </div>
                    <div class="col-md-12 p-2">
                        <div id="contact-message" style="display:none;"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        require(['jquery'], function($) {
            $(document).ready(function () {

                $('#ajax-contact-form').on('submit', function(e) {
                    e.preventDefault();

                    var $form = $(this);
                    var firstName = $form.find('input[name="first_name"]').val();
                    var lastName = $form.find('input[name="last_name"]').val();

                    // Remove old 'name' input if exists
                    $form.find('input[name="name"]').remove();
                    // Merge first + last name into 'name' for Magento backend
                    $form.append('<input type="hidden" name="name" value="' + firstName + ' ' + lastName + '">');

                    var formData = $form.serialize();
                    var $messageBox = $('#contact-message');

                    $.ajax({
                        url: '/contact/index/post', // Magento AJAX controller
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function() {
                            $messageBox.removeClass('error success').text('Submitting...').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                $messageBox.addClass('success').text(response.message).css({'color':'green','font-weight':'600'});
                                $form[0].reset();
                            } else {
                                $messageBox.addClass('error').text(response.message).css({'color':'red','font-weight':'600'});
                            }
                        },
                        error: function(xhr) {
                            $messageBox.addClass('error').text('Something went wrong. Please try again later.').css({'color':'red','font-weight':'600'});
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
        
        ob_start();
        ?>
        <div class="git">
        <div class="get-in-touch main-title">
            <h3 class="getintouch"><?= $settings['title']; ?></h3>

            <form id="ajax-contact-form">
                <div class="row">
                    <div class="col-md-6 p-2">
                        <input type="text" name="first_name" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="First Name*" required>
                    </div>
                    <div class="col-md-6 p-2">
                        <input type="text" name="last_name" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Last Name*" required>
                    </div>
                    <div class="col-md-6 p-2">
                        <input type="email" name="email" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Email*" required>
                    </div>
                    <div class="col-md-6 p-2">
                        <input type="text" name="telephone" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Phone*" required>
                    </div>
                    <div class="col-md-12 p-2">
                        <textarea name="comment" class="form-control form-control-lg rounded-0 placeholder-content" placeholder="Do You Have Any Additional Info?" rows="4" required></textarea>
                    </div>
                    <input type="hidden" name="hideit" value="">
                    <div class="col-md-12 p-2">
                        <button type="submit" class="submit-button mb-3 p-3">Submit</button>
                    </div>
                    <div class="col-md-12 p-2">
                        <div id="contact-message" style="display:none;"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        require(['jquery'], function($) {
            $(document).ready(function () {

                $('#ajax-contact-form').on('submit', function(e) {
                    e.preventDefault();

                    var $form = $(this);
                    var firstName = $form.find('input[name="first_name"]').val();
                    var lastName = $form.find('input[name="last_name"]').val();

                    // Remove old 'name' input if exists
                    $form.find('input[name="name"]').remove();
                    // Merge first + last name into 'name' for Magento backend
                    $form.append('<input type="hidden" name="name" value="' + firstName + ' ' + lastName + '">');

                    var formData = $form.serialize();
                    var $messageBox = $('#contact-message');

                    $.ajax({
                        url: '/contact/index/post', // Magento AJAX controller
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function() {
                            $messageBox.removeClass('error success').text('Submitting...').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                $messageBox.addClass('success').text(response.message).css({'color':'green','font-weight':'600'});
                                $form[0].reset();
                            } else {
                                $messageBox.addClass('error').text(response.message).css({'color':'red','font-weight':'600'});
                            }
                        },
                        error: function(xhr) {
                            $messageBox.addClass('error').text('Something went wrong. Please try again later.').css({'color':'red','font-weight':'600'});
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