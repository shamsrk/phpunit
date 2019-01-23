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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * UserController to handle the user activities like sign up, log in
 */
class UserController extends Controller
{
    /**
     * Function to signup
     *
     * @Route("/signup", name="signup", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function signUpAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $requestData = $request->request->all();

        // Validate request data
        $validator = Validator::validate($requestData, [
            Key::NAME => 'required|max:255|min:5',
            Key::EMAIL => 'required|max:255',
            Key::USERNAME => 'required|max:255|min:5',
            Key::PHONE => 'digits:10',
            Key::ADDRESS => 'string|max:255',
            Key::getPasswordKey() => 'required|string|max:25|min:5'
        ]);

        try {
            if (!$validator->fails()) {
                // Load mongodb manager
                $dm = $this->get(Key::DOCTRINE_MONGODB)->getManager();

                // Validate email uniqueness
                if ((boolean)$dm->getrepository(User::class)->findOneBy([Key::EMAIL => $requestData[Key::EMAIL]])) {
                    GenericService::$response = ErrorConstants::$generalErrors['ALREADY_EXISTS'];
                } else {
                    $user = new User();

                    $password = $passwordEncoder->encodePassword($user, $requestData[Key::getPasswordKey()]);

                    $user->setName($requestData[Key::NAME])
                        ->setEmail($requestData[Key::EMAIL])
                        ->setUsername($requestData[Key::USERNAME])
                        ->setAddress($requestData[Key::ADDRESS])
                        ->setPhoneNumber($requestData[Key::PHONE])
                        ->setDob($requestData[Key::DOB])
                        ->setPassword($password);
                    $dm->persist($user);
                    $dm->flush();

                    unset($requestData[Key::getPasswordKey()]);

                    GenericService::$response = array_merge(
                        MessageConstants::$generalInfo['SIGNUP_SUCCESS'], [Key::DATA => $requestData]
                    );
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

                if (!(boolean)$user) {
                    GenericService::$response = ErrorConstants::$generalErrors['NOT_EXISTS'];
                } else {
                    // Get the encoder for the users password
                    $encoder_service = $this->get('security.encoder_factory');
                    $encoder = $encoder_service->getEncoder($user);

                    // Note the difference
                    if ($encoder->isPasswordValid($user->getPassword(),
                        $requestData[Key::getPasswordKey()], $user->getSalt())) {
                        // update the sessionId, lastActiveAt, devicedId
                        $user->setSessionId(generateToken())
                            ->setLastActiveAt(new \DateTime())
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

    /**
     * Function to get all users in database
     *
     * @Route("/getUsers", name="get_users")
     */
    public function getUsers()
    {
        $dm = $this->get(Key::DOCTRINE_MONGODB)->getManager();
        $users = $dm->getrepository(User::class)->findAll();

        return new JsonResponse(array_map(function ($user) {
            return $user->get();
        }, $users));

    }


    /**
     * Function to fetch user details
     *
     * @Route("/user/details", name="user_details")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userDetailsAction(Request $request)
    {
        $user = $this->get(Key::DOCTRINE_MONGODB)
            ->getManager()->getrepository(User::class)
            ->findOneBy([Key::EMAIL => $request->headers->get(Key::EMAIL)])->get();
        return new JsonResponse($user);
    }
}