<?php
declare(strict_types= 1);
namespace netvod\notification;

class Notification {
    private $message;
    private $type;
    private $titre;
    private $duree;

    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';

    public function __construct(string $message, string $titre,string $type, $duree = 5000) {
        $this->message = $message;
        $this->titre = $titre;
        $this->type = $type;
    }

    public static function save(string $message, string $titre, string $type, $duree=5000): void {
        $_SESSION['notification'] = new Notification($message, $titre, $type, $duree);
    }

    public static function load(): ?Notification {
        if (isset($_SESSION['notification'])) {
            $notification = $_SESSION['notification'];
            unset($_SESSION['notification']);
            return $notification;
        }
        return null;
    }

    public static function render(): string {
        $notification = self::load();
        
        if ($notification === null) {
            return '';
        }
        
        $type = htmlspecialchars($notification->type);
        $title = htmlspecialchars($notification->titre);
        $message = htmlspecialchars($notification->message);
        $duration = intval($notification->duree);
        
        // Icône selon le type
        $icons = [
            'success' => '✓',
            'error' => '✕',
            'warning' => '⚠',
            'info' => 'ℹ'
        ];
        
        $icon = $icons[$type] ?? 'ℹ';
        
        return <<<FIN
            <div class="toast toast-{$type}" id="notificationToast" data-duration="{$duration}">
                <div class="toast-icon">{$icon}</div>
                <div class="toast-content">
                    <h3 class="toast-title">{$title}</h3>
                    <p class="toast-message">{$message}</p>
                </div>
                <button class="toast-close" onclick="closeToast()">✕</button>
            </div>
        FIN;
    }
}