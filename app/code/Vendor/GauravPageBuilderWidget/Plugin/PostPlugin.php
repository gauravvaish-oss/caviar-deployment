<?php
namespace Vendor\GauravPageBuilderWidget\Plugin;

use Magento\Contact\Controller\Index\Post as Subject;
use Magento\Framework\Controller\Result\Redirect;

class PostPlugin
{
    /**
     * Modify redirect path after execute() runs
     *
     * @param Subject $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(Subject $subject, $result)
    {
        // Check if result is a redirect
        if ($result instanceof Redirect) {
            // ✅ Change redirection to contact-us only for this controller
            $result->setPath('contact-us');
        }

        return $result;
    }
}
