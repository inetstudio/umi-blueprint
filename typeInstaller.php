<?php
/**
 * Created by Maxim Seredinskiy, Maxim Rakhmankin, Evgenii Ioffe
 * @author Maxim Rakhmankin, Maxim Seredinskiy <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Maxim Seredinskiy, Maxim Rakhmankin
 */

/** @noinspection PhpIncludeInspection */
require_once dirname(__FILE__) . '/standalone.php';
require_once CURRENT_WORKING_DIR . '/templates/inet/installer/extendedInstaller.php';

use iOutputBuffer as iBuffer;
use UmiCms\Service;

class TypeExtendingInstaller extends ExtendedInstaller {
    /** @var string TYPES_EXTENSIONS_DIR директория для типов */
    const TYPES_EXTENSIONS_DIR = CURRENT_WORKING_DIR . '/templates/inet/types/';

    /** @var iBuffer $buffer Буфер обмена */
    protected iOutputBuffer $buffer;
    /** @var MailNotificationsCollection $mailNotifications */
    protected $mailNotifications;
    /** @var MailTemplatesCollection $mailTemplates  */
    protected $mailTemplates;

    /**
     * TypeExtendingInstaller constructor.
     * @throws coreException
     * @throws publicException
     */
    public function __construct() {
        parent::__construct();

        $this->buffer = Service::Response()->getCurrentBuffer();

        // mail templates services
        $this->mailNotifications = Service::MailNotifications();
        $this->mailTemplates = Service::MailTemplates();
    }

    /** Execute creation in all forms classes */
    public function executeExtensions() {
        // load types interface first
        /** @noinspection PhpIncludeInspection */
        require_once self::TYPES_EXTENSIONS_DIR . "ITypeExtension.php";

        // scan types classes
        $this->scanExtensionsFolder(self::TYPES_EXTENSIONS_DIR);

        // get all extensions
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, __CLASS__)) $this->extensions[] = $class;
        }

        // plug and execute all extensions classes
        self::plugAndExecuteAllExtensions();
    }

    /**
     * Execute extensions creation methods
     */
    private function plugAndExecuteAllExtensions() {
        $executeByPriority = [];

        foreach ($this->extensions as $extension) {
            $extensionClass = new $extension();
            if ($extensionClass instanceof ITypeExtension) {
                // get priority value
                $priority = $extensionClass->getPriority();
                $executeByPriority[$priority][] = $extensionClass;
            }
        }

        ksort($executeByPriority);
        foreach ($executeByPriority as $extensions) {
            array_walk(
                $extensions,
                fn($extension) =>
                    /** @var ITypeExtension $extension */
                    $extension->execute()
            );
        }
    }

    /**
     * @return void
     */
    public function getBuffer() {
        $this->buffer->push(json_encode(['success' => true]));
        $this->buffer->option('generation-time', true);
        $this->buffer->end();
    }
}
$extend = new TypeExtendingInstaller();
$extend->executeExtensions();
$extend->getBuffer();