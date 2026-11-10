import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';

class ChatDetailScreen extends StatefulWidget {
  final String userName;
  final String itemTitle;
  final String initialPrice;

  const ChatDetailScreen({
    super.key,
    required this.userName,
    required this.itemTitle,
    required this.initialPrice,
  });

  @override
  State<ChatDetailScreen> createState() => _ChatDetailScreenState();
}

class _ChatDetailScreenState extends State<ChatDetailScreen> {
  final _textController = TextEditingController();
  final List<Map<String, dynamic>> _messages = [
    {
      'isMe': false,
      'text': 'Hello! Is this item still available for inspection?',
      'time': '10:14 AM',
    },
    {
      'isMe': true,
      'text': 'Yes, absolutely. It is in flawless condition.',
      'time': '10:15 AM',
    },
    {
      'isMe': false,
      'isOffer': true,
      'offerAmount': 'PKR 4,200,000',
      'text': 'Sent an offer: PKR 4,200,000',
      'time': '10:16 AM',
    },
  ];

  void _sendMessage() {
    final text = _textController.text.trim();
    if (text.isEmpty) return;

    setState(() {
      _messages.add({
        'isMe': true,
        'text': text,
        'time': 'Just now',
      });
      _textController.clear();
    });
  }

  void _makeCounterOffer() {
    showDialog(
      context: context,
      builder: (ctx) {
        final offerCtrl = TextEditingController(text: '4350000');
        return AlertDialog(
          title: const Text('Send Counter Offer'),
          content: TextField(
            controller: offerCtrl,
            decoration: const InputDecoration(labelText: 'Amount (PKR)', prefixText: 'Rs. '),
            keyboardType: TextInputType.number,
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.champagne, foregroundColor: AppTheme.obsidian),
              onPressed: () {
                Navigator.pop(ctx);
                setState(() {
                  _messages.add({
                    'isMe': true,
                    'isOffer': true,
                    'offerAmount': 'PKR ${offerCtrl.text}',
                    'text': 'Counter-offer: PKR ${offerCtrl.text}',
                    'time': 'Just now',
                  });
                });
              },
              child: const Text('Submit Offer'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(widget.userName, style: const TextStyle(fontSize: 16)),
            Text(widget.itemTitle, style: const TextStyle(fontSize: 11, color: AppTheme.champagne)),
          ],
        ),
      ),
      body: Column(
        children: [
          // Item Bar
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            color: AppTheme.obsidian,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(widget.initialPrice, style: const TextStyle(color: AppTheme.champagne, fontWeight: FontWeight.bold)),
                TextButton.icon(
                  style: TextButton.styleFrom(foregroundColor: AppTheme.champagne),
                  icon: const Icon(Icons.local_offer_outlined, size: 16),
                  label: const Text('Counter Offer', style: TextStyle(fontWeight: FontWeight.bold)),
                  onPressed: _makeCounterOffer,
                ),
              ],
            ),
          ),

          // Messages List
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(AppTheme.space16),
              itemCount: _messages.length,
              itemBuilder: (context, index) {
                final msg = _messages[index];
                final isMe = msg['isMe'] as bool;
                final isOffer = msg['isOffer'] == true;

                return Align(
                  alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      color: isOffer
                          ? AppTheme.champagneLight
                          : (isMe ? AppTheme.obsidian : Colors.white),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: isOffer ? AppTheme.champagneDark : (isMe ? Colors.transparent : AppTheme.borderSubtle),
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.04),
                          blurRadius: 4,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: isMe ? CrossAxisAlignment.end : CrossAxisAlignment.start,
                      children: [
                        if (isOffer) ...[
                          const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.handshake_outlined, size: 16, color: AppTheme.champagneDark),
                              SizedBox(width: 4),
                              Text('OFFER', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.champagneDark)),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Text(
                            msg['offerAmount'] as String,
                            style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.obsidian),
                          ),
                          const SizedBox(height: 4),
                        ],
                        Text(
                          msg['text'] as String,
                          style: TextStyle(
                            color: isOffer ? AppTheme.obsidian : (isMe ? Colors.white : AppTheme.textPrimary),
                            fontSize: 14,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          msg['time'] as String,
                          style: TextStyle(
                            color: isMe ? Colors.white60 : AppTheme.textSecondary,
                            fontSize: 10,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),

          // Message Input Bar
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: AppTheme.borderSubtle)),
            ),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _textController,
                    decoration: const InputDecoration(
                      hintText: 'Type your message...',
                      border: InputBorder.none,
                      contentPadding: EdgeInsets.symmetric(horizontal: 12),
                    ),
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.send_rounded, color: AppTheme.champagneDark),
                  onPressed: _sendMessage,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
