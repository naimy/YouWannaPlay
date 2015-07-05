<?php foreach($chats as $chat){?>
<p><?php echo $chat->date; ?> - <?php echo date('H:i:s', $chat->time); ?> - <?php echo $chat->name; ?> : <?php echo $chat->content; ?></p>
<?php } ?>