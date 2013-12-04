<?php
/**
 * App_Validate_GroupMail - klasa walidatora maili oddzielonych DELIMITER'em
 * 
 * Łukasz Ciołecki
 */
class Extlib_Validate_GroupMail extends Zend_Validate_Abstract
{
    const TOO_MUCH  = 'groupEmailTooMuch';
    const INCORECT_MAIL = 'invalidGroupMail';
    
    const EMAIL_DELIMITER = ',';
   
    /* Spacja  */
    const ASCII_32 = ' ';
    
    /**
     * $_maxCount - maksymalna ilość adresów email
     * 
     * @var int 
     */
    protected $_maxCount = 1000;

    /**
     * $_messageTemplates - tablica komunikatów
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::TOO_MUCH  => "Maksymalna liczba emaili to '%value%'",
        self::INCORECT_MAIL => "Wskazane adresy email są niepoprawne: '%value%'"
    );

    /**
     * __construct() - instancja konstruktora
     * 
     * @param type $maxCount 
     */
    public function __construct($maxCount = null)
    {
        if (null !== $maxCount) {
            $this->setMaxCount($maxCount);
        }
    }
    
    /**
     * isValid() - strategy pattern (implementacja wzorca strategii)
     * 
     * @param string $value
     * @return boolean 
     */
    public function isValid($value) 
    {       
        $zendEmailValidator = new Zend_Validate_EmailAddress();
        
        $value = explode(self::EMAIL_DELIMITER, trim($value, self::EMAIL_DELIMITER)); 

        if (count($value) > $this->getMaxCount()) {
            $this->_error(self::TOO_MUCH, $this->getMaxCount());
            return false;    
        }

        $wrongEmails = false;
        
        foreach($value as $email) {
            $email = trim($email, self::ASCII_32); 
            if (!$zendEmailValidator->isValid($email)) {
                $wrongEmails .= self::EMAIL_DELIMITER . ' ' . $email;
            }
        }

        if ($wrongEmails) {
            $this->_error(self::INCORECT_MAIL, trim($wrongEmails, self::EMAIL_DELIMITER));
            return false;
        }
        
        return true;
    } 
    
    /**
     * getMaxCount() - metoda zwracająca maksymalną ilość emaili
     * 
     * @return type 
     */
    public function getMaxCount() 
    {
        return $this->_maxCount;
    }

    /**
     * setMaxCount() - metoda ustawiająca maksymalną ilość emaili
     * 
     * @param int $maxCount 
     */
    public function setMaxCount($maxCount) 
    {
        $this->_maxCount = $maxCount;
    }
}