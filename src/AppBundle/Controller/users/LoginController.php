<?php

namespace AppBundle\Controller\users;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\MessageConstants;
use AppBundle\Document\User;
use AppBundle\Services\GenericService;
use AppBundle\Services\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Constants\KeyConstants as Key;

/**
 * LoginController to handle the user activities like log in
 */
class LoginController extends Controller
{
    /**
     * Function to signin
     *
     * @Route("/signin", name="signin", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function loginAction(Request $request)
    {
        $requestData = $request->request->all();

        // Validate request data
        $validator = Validator::validate($requestData, [
            Key::USERNAME => Key::REQUIRED,
            Key::getPasswordKey() => Key::REQUIRED,
            Key::DEVICE_ID => Key::REQUIRED
        ]);

        try {

            if (!$validator->fails()) {

                // Load mongodb manager
                $dm = $this->get(Key::DOCTRINE_MONGODB)->getManager();

                // Get user data, if present in database
                $user = $dm->getrepository(User::class)->findOneBy([Key::EMAIL => $requestData[Key::USERNAME]]);

                if (!$user) {
                    GenericService::$response = ErrorConstants::$generalErrors['NOT_EXISTS'];
                } else {
                    // Get the encoder for the users password
                    $encoder_service = $this->get('security.encoder_factory');
                    $encoder = $encoder_service->getEncoder($user);

                    // Note the difference
                    if ($encoder->isPasswordValid($user->getPassword(),
                        $requestData[Key::getPasswordKey()], $user->getSalt())) {

                        // Check if already not logged in, then create session id
                        if (!(trim($user->getSessionId()) &&
                            ((new \DateTime())->diff($user->getLastActiveAt())->h) <= 24)) {
                            $user->setSessionId(generateToken());
                        }

                        // update the lastActiveAt, devicedId
                        $user->setLastActiveAt(new \DateTime())
                            ->setDeviceId($requestData[Key::DEVICE_ID]);

                        $dm->persist($user);
                        $dm->flush();

                        GenericService::$response = array_merge(
                            MessageConstants::$generalInfo['SIGNIN_SUCCESS'], [Key::DATA => $user->get()]
                        );

                    } else {
                        // Password invalid
                        GenericService::$response = ErrorConstants::$generalErrors['INVALID_PASSWORD'];
                    }
                }

            } else {
                GenericService::$response = array_merge(
                    ErrorConstants::$generalErrors['VALIDATION_FAIL'], [Key::DATA => $validator->validationErrors()]
                );
            }

        } catch (\Exception $exception) {

            GenericService::$response[Key::MESSAGE] = $exception->getMessage();

        }

        return new JsonResponse(GenericService::getResponse(
            $this->get('translator')->trans(GenericService::$response[Key::MESSAGE]))
        );
    }
}