<?php

namespace CanalTP\SamEcoreSecurityBundle\Test;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Tests\Authorization\Voter\RoleVoterTest;
use CanalTP\SamEcoreSecurityBundle\Security\Authorization\Voter\ApplicationVoter;

class ApplicationVoterTest extends RoleVoterTest
{
    /**
     * @dataProvider getVoteTests
     */
    public function testVote($roles, $attributes, $expected)
    {
        $voter = new ApplicationVoter();

        $this->assertSame($expected, $voter->vote($this->getToken($roles), null, $attributes));
    }

    public function getApplicationVoteTests()
    {
        return array(
            array(array(), array(), VoterInterface::ACCESS_ABSTAIN),
            array(array(), array('FOO'), VoterInterface::ACCESS_ABSTAIN),
            array(array(), array('APP_FOO'), VoterInterface::ACCESS_DENIED),
            array(array('APP_FOO'), array('APP_FOO'), VoterInterface::ACCESS_GRANTED),
            array(array('APP_FOO'), array('FOO', 'APP_FOO'), VoterInterface::ACCESS_GRANTED),
            array(array('APP_BAR', 'APP_FOO'), array('APP_FOO'), VoterInterface::ACCESS_GRANTED),
        );
    }

    public function getVoteTests()
    {
        return array_merge($this->getApplicationVoteTests(), array(
            array(array('APP_FOO'), array('APP_FOOBAR'), VoterInterface::ACCESS_DENIED),
        ));
    }
}
