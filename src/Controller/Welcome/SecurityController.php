<?php

namespace App\Controller\Welcome;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleAuthenticatorTwoFactorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'welcome_app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('welcome/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);

    }

    #[Route(path: '/logout', name: 'welcome_app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/2fa/qrcode', name: '2fa_qrcode')]
    public function displayGoogleAuthenticatorQrCode(GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        $user = $this->getUser();
        if (!($user instanceof GoogleAuthenticatorTwoFactorInterface)) {
            throw new NotFoundHttpException('Cannot display QR code');
        }

        return $this->displayQrCode($googleAuthenticator->getQRContent($user));
    }


    public function displayQrCode(string $qrCodeContent): Response
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            data:$qrCodeContent,
            encoding:new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size:200,
            margin:0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );
        $result = $builder->build();
        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }


    #[Route('/2fa', name: '2fa_login')]
    public function displayGoogleAuthenticator(): Response
    {
        return $this->render('welcome/security/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qrcode'),
        ]);
    }


}
