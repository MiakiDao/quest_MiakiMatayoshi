<?php
class Card {
    public string $suit;
    public string $number;

    public function __construct(string $suit, string $number) {
        $this->suit = $suit;
        $this->number = $number;
    }

    public function display(): string {
        return "{$this->suit}の{$this->number}";
    }
}

class Card_generate {
    public static function generateCard(): array {
        $cards = [];
        $suits = ['ハート', 'ダイヤ', 'スペード', 'クラブ'];
        $numbers = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
        foreach ($suits as $suit) {
            foreach ($numbers as $number) {
                $cards[] = new Card($suit, $number);
            }
        }
        return $cards;
    }
}

class Player {
    public string $name;
    public int $my_number;
    public array $hand = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function receiveCard(Card $card) {
        $this->hand[] = $card;
    }

    public function showCard() {
        $my_number = array_pop($this->hand);
        return $my_number;
    }
}

class Deck {
    public array $shuffled_card = [];
    public Player $player1;
    public Player $player2;

    public function __construct(Player $player1, Player $player2) {
        $this->player1 = $player1;
        $this->player2 = $player2;

 // Card_generate::generate_code()で生成したカードをシャッフル
        $field = Card_generate::generateCard();
        shuffle($field);
        $this->shuffled_card = $field;

// シャッフルしたカードをプレイヤーに交互に配る
        while (!empty($this->shuffled_card)) {
            $this->player1->receiveCard(array_pop($this->shuffled_card));
            $this->player2->receiveCard(array_pop($this->shuffled_card));
        }
    }
}

class GameMessage {
    public function __construct() {
        echo "戦争を開始します。\n";  
        echo "カードが配られました。\n";

        while (true) {
            echo "『戦争』の掛け声で両者のカードを見せ合ってください\n";
            $input = trim(fgets(STDIN));
            if ($input === '戦争') {
                break;
            }
            echo "『戦争』と入力してください。\n";
        }
    }
}

class GameHost {
    private array $valueMap = [
        'A' => 14, 'K' => 13, 'Q' => 12, 'J' => 11, '10' => 10, '9' => 9, 
        '8' => 8, '7' => 7, '6' => 6, '5' => 5, '4' => 4, '3' => 3, '2' => 2
    ];

    public function showEachCard(Player $player1, Player $player2): void {
        $card1 = $player1->showCard();
        $card2 = $player2->showCard();
        echo "$player1->name のカードは $card1->suit $card1->number です。\n";
        echo "$player2->name のカードは $card2->suit $card2->number です。\n";

        if ($card1->number > $card2->number) {
            echo "$player1->name の勝利です。\n";
        } elseif ($card1->number < $card2->number) {
            echo "$player2->name の勝利です。\n";
        } else {
            echo "引き分けです。\n";
        }
    }
}

$player1 = new Player("プレイヤー１");
$player2 = new Player("プレイヤー２");

$deck = new Deck($player1, $player2);

$message = new GameMessage();

$host = new GameHost();
$host->showEachCard($player1, $player2);
