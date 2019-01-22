<?php
/**
 * UserController to handle the user activities like sign up, log in
 */

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

class UserController extends Controller
{
    /**
     * Function to signup
     *
     * @Route("/signup", name="create_product")
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
            Key::PHONE => 'digits:10',
            Key::ADDRESS => 'string|max:255',
            Key::getPasswordKey() => 'required|string|max:25|min:5'
        ]);

        try {
            if (!$validator->fails()) {
                // Load mongodb manager
                $dm = $this->get('doctrine_mongodb')->getManager();

                // Validate email uniqueness
                if ((boolean)$dm->getrepository(User::class)->findOneBy([Key::EMAIL => $requestData[Key::EMAIL]])) {
                    GenericService::$response = ErrorConstants::$generalErrors['ALREADYEXISTS'];
                } else {
                    $user = new User();

                    $password = $passwordEncoder->encodePassword($user, $requestData[Key::getPasswordKey()]);

                    $user->setName($requestData[Key::NAME])
                        ->setEmail($requestData[Key::EMAIL])
                        ->setAddress($requestData[Key::ADDRESS])
                        ->setPhoneNumber($requestData[Key::PHONE])
                        ->setDob($requestData[Key::DOB])
                        ->setPassword($password);
                    $dm->persist($user);
                    $dm->flush();

                    unset($requestData[Key::getPasswordKey()]);

                    GenericService::$response = array_merge(
                        MessageConstants::$generalInfo['SIGNUPSUCCESS'], [Key::DATA => $requestData]
                    );
                }

            } else {
                GenericService::$response = array_merge(
                    ErrorConstants::$generalErrors['VALIDATIONFAIL'], [Key::DATA => $validator->validationErrors()]
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
        $dm = $this->get('doctrine_mongodb')->getManager();
        $users = $dm->getrepository(User::class)->findAll();
        dd($users);
        return new JsonResponse($users);

    }
}