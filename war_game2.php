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
    public array $hand = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function receiveCard(Card $card) {
        $this->hand[] = $card;
    }

    public function showCard() {
        return array_pop($this->hand);
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
    public string $message;

    public function __construct() {
        echo "戦争を開始します。\n";  
        echo "カードが配られました。\n";
    }

    public function sayGameStart(): void {
        echo "戦争!\n";
    }

    public function sayGameEnd(Player $loser, Player $winner): void {
        echo "{$loser->name} のカードがなくなりました。{$winner->name} が1位、{$loser->name} が2位です。\n";
    }
}



class GameHost {
    private array $valueMap = [
        'A' => 14, 'K' => 13, 'Q' => 12, 'J' => 11,
        '10' => 10, '9' => 9, '8' => 8, '7' => 7,
        '6' => 6, '5' => 5, '4' => 4, '3' => 3, '2' => 2
    ];

    public function showEachCard(Player $player1, Player $player2, GameMessage $message): void {
        while (!empty($player1->hand) && !empty($player2->hand)) {
            $message->sayGameStart();

        $card1 = $player1->showCard();
        $card2 = $player2->showCard();

        echo "{$player1->name} のカードは {$card1->display()} です。\n";
        echo "{$player2->name} のカードは {$card2->display()} です。\n";

        $value1 = $this->valueMap[$card1->number];
        $value2 = $this->valueMap[$card2->number];

        if ($value1 > $value2) {
            echo "{$player1->name} の勝利です。\n";
            echo "{$player1->name} はカードを2枚もらいました。\n";
            echo "{$player1->name} のカードの合計枚数は " . count($player1->hand) . " 枚です。\n";
            echo "{$player2->name} のカードの合計枚数は " . count($player2->hand) . " 枚です。\n";
            $player1->receiveCard($card1);
            $player1->receiveCard($card2); 
        } elseif ($value2 > $value1) {
            echo "{$player2->name} の勝利です。\n";
            echo "{$player2->name} はカードを2枚もらいました。\n";
            echo "{$player1->name} のカードの合計枚数は " . count($player1->hand) . " 枚です。\n";
            echo "{$player2->name} のカードの合計枚数は " . count($player2->hand) . " 枚です。\n";
            $player2->receiveCard($card1);
            $player2->receiveCard($card2);
        } else {
            echo "引き分けです。\n";
            }

            if (empty($player1->hand)) {
                echo $message->sayGameEnd($player1, $player2);
            } elseif (empty($player2->hand)) {
                echo $message->sayGameEnd($player2, $player1);
            }
        }
    }
}

$player1 = new Player("プレイヤー１");
$player2 = new Player("プレイヤー２");

$deck = new Deck($player1, $player2);

$message = new GameMessage();

$host = new GameHost();
$host->showEachCard($player1, $player2, $message);
