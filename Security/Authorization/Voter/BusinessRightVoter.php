<?php

namespace CanalTP\SamEcoreSecurityBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Session;

class BusinessRightVoter extends Voter
{
    private $prefix;
    private $om;
    private $appFinder;

    public function __construct(ObjectManager $om, $appFinder)
    {
        $this->prefix = 'BUSINESS_';
        $this->om = $om;
        $this->appFinder = $appFinder;
    }

    public function supports($attribute, $subject)
    {
        return 0 === strpos($attribute, $this->prefix);
    }

    private function checkPermission($attribute, $permissions)
    {
        if ($permissions == null) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($attribute === $permission) {
                return true;
            }
        }
        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($token->getUser() == 'anon.') {
            return false;
        }

        $user = $token->getUser();
        if (in_array('ROLE_API', $user->getRoles())) {
            return true;
        }
        if ($user->isSuperAdmin()) {
            return true;
        }

        $roles = $this->extractRoles($token);
        if (!$this->supports($attribute, $subject)) {
            return false;
        }

        $result = false;
        foreach ($roles as $role) {
            if ($this->checkPermission($attribute, $role->getPermissions())) {
                return true;
            }
        }

        return $result;
    }

    protected function extractRoles(TokenInterface $token)
    {
        return $this->om
            ->getRepository('CanalTPSamCoreBundle:Role')->findRolesByUserAndApplication(
                $token->getUser()->getId(),
                $this->appFinder->findFromUrl()->getCanonicalName()
            );
    }
}
