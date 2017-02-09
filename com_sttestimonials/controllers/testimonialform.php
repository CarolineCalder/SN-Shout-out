<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Sttestimonials
 * @author     Sopan Technologies <info@sopantech.com>
 * @copyright  2016 Sopan Technologies
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

/**
 * Testimonial controller class.
 *
 * @since  1.6
 */
class SttestimonialsControllerTestimonialForm extends JControllerForm
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	public function edit()
	{
		$app = JFactory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_sttestimonials.edit.testimonial.id');
		$editId     = $app->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_sttestimonials.edit.testimonial.id', $editId);

		// Get the model.
		$model = $this->getModel('TestimonialForm', 'SttestimonialsModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_sttestimonials&view=testimonialform&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  1.6
	 */
	public function save()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
		// Initialise variables.
		$app   = JFactory::getApplication();
		$model = $this->getModel('TestimonialForm', 'SttestimonialsModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			throw new Exception($model->getError(), 500);
		}
        
        // check for empty editor .. contains empty p tags
        $testimonial_text = strip_tags($data[testimonial]);
        if($testimonial_text === "") $data[testimonial] = null;
        
        // set date manually for front-end submissions
        $data[date] = date('Y-m-d');

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$input = $app->input;
			$jform = $input->get('jform', array(), 'ARRAY');

			// Save the data in the session.
			$app->setUserState('com_sttestimonials.edit.testimonial.data', $jform);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_sttestimonials.edit.testimonial.id');
			$this->setRedirect(JRoute::_('index.php?option=com_sttestimonials&view=testimonialform&layout=edit&id=' . $id, false));
		}
        
        // Check captcha response
        if (!empty($_POST) && isset($_POST['g-recaptcha-response'])) {
            $captchaResponse = $this->verifyCaptcha($_POST['g-recaptcha-response']);
            if($captchaResponse === false) {
                    
                $app->enqueueMessage('Captcha check failed. Please try again.', 'warning');
                
                // Save the data in the session.
                $app->setUserState('com_sttestimonials.edit.testimonial.data', $jform);
                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=com_sttestimonials&view=testimonialform&layout=edit', false));
    
            }
        }

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_sttestimonials.edit.testimonial.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_sttestimonials.edit.testimonial.id');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_sttestimonials&view=testimonialform&layout=edit&id=' . $id, false));
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
            // Send notification to admin if submitted from front-end
            $this->notifyAdmin($data);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_sttestimonials.edit.testimonial.id', null);

		// Redirect to the list screen.
		$this->setMessage(JText::_('COM_STTESTIMONIALS_ITEM_SAVED_SUCCESSFULLY'));
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_sttestimonials&view=testimonials' : $item->link);
		$this->setRedirect(JRoute::_($url, false));

		// Flush the data from the session.
		$app->setUserState('com_sttestimonials.edit.testimonial.data', null);
	}

	/**
	 * Method to abort current operation
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function cancel()
	{
		$app = JFactory::getApplication();

		// Get the current edit id.
		$editId = (int) $app->getUserState('com_sttestimonials.edit.testimonial.id');

		// Get the model.
		$model = $this->getModel('TestimonialForm', 'SttestimonialsModel');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_sttestimonials&view=testimonials' : $item->link);
		$this->setRedirect(JRoute::_($url, false));
	}

	/**
	 * Method to remove data
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function remove()
	{
		// Initialise variables.
		$app   = JFactory::getApplication();
		$model = $this->getModel('TestimonialForm', 'SttestimonialsModel');

		// Get the user data.
		$data       = array();
		$data['id'] = $app->input->getInt('id');

		// Check for errors.
		if (empty($data['id']))
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_sttestimonials.edit.testimonial.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_sttestimonials.edit.testimonial.id');
			$this->setRedirect(JRoute::_('index.php?option=com_sttestimonials&view=testimonial&layout=edit&id=' . $id, false));
		}

		// Attempt to save the data.
		$return = $model->delete($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_sttestimonials.edit.testimonial.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_sttestimonials.edit.testimonial.id');
			$this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_sttestimonials&view=testimonial&layout=edit&id=' . $id, false));
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_sttestimonials.edit.testimonial.id', null);

		// Redirect to the list screen.
		$this->setMessage(JText::_('COM_STTESTIMONIALS_ITEM_DELETED_SUCCESSFULLY'));
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_sttestimonials&view=testimonials' : $item->link);
		$this->setRedirect(JRoute::_($url, false));

		// Flush the data from the session.
		$app->setUserState('com_sttestimonials.edit.testimonial.data', null);
	}

    private function verifyCaptcha($verifyresponse)
    {
        // Get component params
        $params = JComponentHelper::getParams('com_sttestimonials');
        
        // Construct the Google verification API request link.
        $request = array();
        $request['secret'] = trim($params->get('captcha_secret_key')); // Secret key
        if (isset($verifyresponse)) {
            $request['response'] = urlencode($verifyresponse);
        }
        $request['remoteip'] = $_SERVER['REMOTE_ADDR'];
    
        $request_string = http_build_query($request);
        $requestURL = 'https://www.google.com/recaptcha/api/siteverify?' . $request_string;
    
        // Get cURL resource
        $curl = curl_init();
    
        // Set some options
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $requestURL,
        ));
    
        // Send the request
        $response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
    
        $response = @json_decode($response, true);
        
        echo '<pre>';print_r($response);
    
        if ($response["success"] == true) {
            return true;
        } else {
            return false;
        }
    }

    private function notifyAdmin($testimonial)
    {
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        $sender = array( 
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' ) 
        );         
        $mailer->setSender($sender);
        $mailer->addRecipient($config->get( 'mailfrom' ));
        $mailer->setSubject('New Testimonial Submitted');
        $body = "A testimonial has been submitted as per the details below. Please review and take appropriate action.<br><br>"
            . "Author: " . $testimonial['author'] . "<br>"
            . "Author Credentials: " . $testimonial['author_credentials'] . "<br>"
            . "Testimonial: " . strip_tags(substr($testimonial['testimonial'], 0, 200), '<p><a><div>') . "<br><br>"
            . "<a href='" . JURI::base() . "/administrator/index.php?option=com_sttestimonials'>Review Testimonial</a><br><br>"
            . "Sent from ST Testimonials";
            
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);
        $send = $mailer->Send();
        if ( $send !== true ) {
            echo 'Error sending email: ' . $send->__toString();
        } else {
            echo 'Mail sent';
        }
    }
}
