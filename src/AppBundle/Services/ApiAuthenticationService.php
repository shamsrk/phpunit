<?php

/*
 * File for Api authentication service.
 *
 * @author <mdshamsreza69@gmail.com>
 *
 * @category Service
 */

namespace AppBundle\Services;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Constants\KeyConstants as Key;

/**
 * ApiAuthenticationService service for api authentication check.
 */
class ApiAuthenticationService
{
    /*
     * Mongo DB Document manager
     *
     * @var DocumentManager $mongoManager
     */
    private $mongoManager;

    /*
     * LoggerInterface to log message
     *
     * @var LoggerInterface $logger
     */
    private $logger;

    /*
     * TranslatorInterface to use translation in the service
     *
     * @var TranslatorInterface $translator
     */
    private $translator;

    /**
     * GenericService constructor.
     * @param DocumentManager $mongoManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        DocumentManager $mongoManager, LoggerInterface $logger, TranslatorInterface $translator
    )
    {
        $this->mongoManager = $mongoManager;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Function to check authentication
     *
     * @param Request $request
     * @return array|bool
     * @throws \Exception
     */
    public function checkAuthentication(Request $request)
    {
        $headers = [
            Key::SESSION_ID => $request->headers->get(Key::SESSION_ID),
            Key::EMAIL => $request->headers->get(Key::EMAIL)
        ];

        $validator = Validator::validate($headers, [
            Key::SESSION_ID => 'required',
            Key::EMAIL => 'required|email'
        ]);

        // Invalid headers
        if ($validator->fails()) {
            return array_merge(
                ErrorConstants::$generalErrors['INVALID_HEADERS'],
                [Key::MESSAGE => $this->translator->trans(
                    ErrorConstants::$generalErrors['INVALID_HEADERS'][Key::MESSAGE]
                )]
            );
        }

        // Check in database, if user is already authenticated
        $user = $this->mongoManager->getrepository(User::class)
            ->findOneBy([Key::EMAIL => $headers[Key::EMAIL], 'sessionId' => $headers[Key::SESSION_ID]]);

        // User not authenticated, User can be active for 24 hours from last activity
        if (!(boolean)$user || ((new \DateTime())->diff($user->getLastActiveAt())->h) > 24) {
            return array_merge(
                ErrorConstants::$generalErrors['NOT_AUTHENTICATED'],
                [Key::MESSAGE => $this->translator->trans(
                    ErrorConstants::$generalErrors['NOT_AUTHENTICATED'][Key::MESSAGE]
                )]
            );
        }

        // Update the last active at time
        $user->setLastActiveAt(new \DateTime());
        $this->mongoManager->persist($user);
        $this->mongoManager->flush();

        return true;
    }
}