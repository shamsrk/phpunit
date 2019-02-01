<?php

namespace AppBundle\Controller\users;

use AppBundle\Constants\MessageConstants;
use AppBundle\Document\User;
use AppBundle\Services\GenericService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Constants\KeyConstants as Key;

/**
 * UserController to handle the user activities like sign up, log in
 */
class UserController extends Controller
{
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

        GenericService::$response = array_merge(
            MessageConstants::$generalInfo['REQUEST_SUCCESS'], [Key::DATA => $user]
        );

        return new JsonResponse(GenericService::getResponse(
            $this->get('translator')->trans(GenericService::$response[Key::MESSAGE], ['%key%' => __FUNCTION__]))
        );
    }
}