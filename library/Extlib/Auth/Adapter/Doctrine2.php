<?php

namespace Extlib\Auth\Adapter;


/**
 * Auth adapter for Doctrine v 2.*
 *
 * @category        Extlib
 * @package         Extlib\Auth
 * @subpackage      Extlib\Auth\Adapter
 * @author          Loïc Frering <loic.frering@gmail.com> (https://github.com/loicfrering/losolib/blob/master/src/LoSo/Zend/Auth/Adapter/Doctrine2.php)
 * @modification    Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 */
class Doctrine2 implements \Zend_Auth_Adapter_Interface
{
    /**
     * Doctrine EntityManager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The entity name to check for an identity.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Identity string
     *
     * @var string
     */
    protected $identity;

    /**
     * Column to be used as identity.
     *
     * @var string
     */
    protected $identityColumn;

    /**
     * Credential string
     *
     * @var string
     */
    protected $credential;

    /**
     * The column to be used as credential.
     *
     * @var string
     */
    protected $credentialColumn;

    /**
     * Treatment applied to the credential, such as MD5() or PASSWORD()
     *
     * @var string
     */
    protected $credentialTreatment = null;

    /**
     * Additional conditions by: field => value
     *
     * @var array
     */
    protected $conditions = array();

    /**
     * Constructor sets configuration options.
     *
     * @param \Doctrine\ORM\EntiyManager
     * @param string
     * @param string
     * @param string
     * @return void
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, $entityName = null, $identityColumn = null, $credentialColumn = null)
    {
        $this->em = $em;

        if (null !== $entityName) {
            $this->setEntityName($entityName);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }
    }

    /**
     * Return EnityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Set EnityManager
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * Set entity name.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
        return $this;
    }

    /**
     * Set identity column.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setIdentityColumn($identityColumn)
    {
        $this->identityColumn = $identityColumn;
        return $this;
    }

    /**
     * Return identity column name
     *
     * @return string
     */
    public function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    /**
     * Set credential column.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * Return credential column name
     *
     * @return string
     */
    public function getCredentialColumn()
    {
        return $this->credentialColumn;
    }

    /**
     * Set the value to be used as identity.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Return identity string
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Set the value to be used as credential.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Get addionals conditions
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Sset addionals conditions
     *
     * @param array $conditions
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }


    /**
     * Add conditions, merge existings with new
     *
     * @param array $add
     */
    public function addConditions(array $add)
    {
        $this->conditions = array_merge($this->conditions, $add);
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a parameterized string that is
     * used to transform or treat the input credential data
     *
     * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or algorithm. By specifying a
     * parameterized treatment string with this method, a developer may apply arbitrary SQL
     * upon input credential data.
     *
     * Examples:
     *
     *  'PASSWORD(?)'
     *  'MD5(?)'
     *
     * @param  string $treatment
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setCredentialTreatment($treatment)
    {
        $this->credentialTreatment = $treatment;
        return $this;
    }

    /**
     * Defined by Zend_Auth_Adapter_Interface. This method is called to
     * attempt an authentication. Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws \Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return \Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        $query = $this->_getQuery();

        $authResult = array(
            'code' => \Zend_Auth_Result::FAILURE,
            'identity' => null,
            'messages' => array()
        );

        try {
            $result = $query->execute();

            $resultCount = count($result);
            if ($resultCount > 1) {
                $authResult['code'] = \Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
                $authResult['messages'][] = 'More than one entity matches the supplied identity.';
            } else if ($resultCount < 1) {
                $authResult['code'] = \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
                $authResult['messages'][] = 'A record with the supplied identity could not be found.';
            } else if (1 == $resultCount) {
                if ($result[0][$this->credentialColumn] != $this->credential) {
                    $authResult['code'] = \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                    $authResult['messages'][] = 'Supplied credential is invalid.';
                } else {
                    $authResult['code'] = \Zend_Auth_Result::SUCCESS;
                    $authResult['identity'] = $this->identity;
                    $authResult['messages'][] = 'Authentication successful.';
                }
            }
        } catch (\Doctrine\ORM\Query\QueryException $qe) {
            $authResult['code'] = \Zend_Auth_Result::FAILURE_UNCATEGORIZED;
            $authResult['messages'][] = $qe->getMessage();
        }

        return new \Zend_Auth_Result(
            $authResult['code'],
            $authResult['identity'],
            $authResult['messages']
        );
    }

    /**
     * This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws \Zend_Auth_Adapter_Exception - in the event that setup was not done properly
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if (null === $this->em || !$this->em instanceof \Doctrine\ORM\EntityManager) {
            $exception = 'A Doctrine2 EntityManager must be supplied for the Zend_Auth_Adapter_Doctrine2 authentication adapter.';
        } elseif (empty($this->identityColumn)) {
            $exception = 'An identity field must be supplied for the Zend_Auth_Adapter_Doctrine2 authentication adapter.';
        } elseif (empty($this->credentialColumn)) {
            $exception = 'A credential field must be supplied for the Zend_Auth_Adapter_Doctrine2 authentication adapter.';
        } elseif (empty($this->identity)) {
            $exception = 'A value for the identity was not provided prior to authentication with Zend_Auth_Adapter_Doctrine2.';
        } elseif (empty($this->credential)) {
            $exception = 'A credential value was not provided prior to authentication with Zend_Auth_Adapter_Doctrine2.';
        }

        if (null !== $exception) {
            /**
             * @see \Zend_Auth_Adapter_Exception
             */
            throw new \Zend_Auth_Adapter_Exception($exception);
        }
    }

    /**
     * Construct the Doctrine query.
     *
     * @TODO credentialTreatment query
     * @return \Doctrine\ORM\Query
     */
    protected function _getQuery()
    {
        /**

        if (empty($this->credentialTreatment) || (strpos($this->credentialTreatment, "?") === false)) {
            $this->credentialTreatment = '?';
        }

        $select = '(CASE WHEN' . $this->credentialColumn . ' = ' . str_replace('?', $this->getEm()->getConnection()->quote($this->credential), $this->credentialTreatment) . ') AS zend_auth_credential_match';
        */

        $allParameters = array('identity' => $this->getIdentity());

        $qb = $this->em->createQueryBuilder()
                       ->select('e.' . $this->credentialColumn)
                       ->from($this->entityName, 'e')
                       ->where('e.' . $this->identityColumn . ' = :identity');

        foreach ($this->conditions as $condition => $parameters) {
            $qb->andWhere('e.' . $condition);
            $allParameters = array_merge($allParameters, $parameters);
        }

        $qb->setParameters($allParameters);

        return $qb->getQuery();
    }
}