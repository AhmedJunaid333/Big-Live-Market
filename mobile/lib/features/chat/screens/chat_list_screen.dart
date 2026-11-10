import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import 'chat_detail_screen.dart';

class ChatListScreen extends StatelessWidget {
  const ChatListScreen({super.key});

  static const List<Map<String, dynamic>> _threads = [
    {
      'name': 'Malik Tariq (Verified Seller)',
      'item': 'Toyota Land Cruiser V8 ZX',
      'price': 'PKR 38,500,000',
      'lastMessage': 'Sent an offer: PKR 37,000,000',
      'time': '10:16 AM',
      'unread': 2,
    },
    {
      'name': 'Usman Electronics',
      'item': 'iPhone 15 Pro Max 256GB Dual SIM',
      'price': 'PKR 365,000',
      'lastMessage': 'Yes, box and warranty card available.',
      'time': 'Yesterday',
      'unread': 0,
    },
    {
      'name': 'DHA Prime Properties',
      'item': '1 Kanal Luxury Designer Villa DHA 6',
      'price': 'PKR 85,000,000',
      'lastMessage': 'Can schedule a walkthrough this Saturday.',
      'time': 'Nov 8',
      'unread': 0,
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Direct Inquiries & Chats'),
      ),
      body: ListView.separated(
        itemCount: _threads.length,
        separatorBuilder: (_, __) => const Divider(height: 1),
        itemBuilder: (context, index) {
          final chat = _threads[index];
          final unread = (chat['unread'] ?? 0) as int;

          return ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            leading: CircleAvatar(
              radius: 26,
              backgroundColor: AppTheme.obsidian,
              child: Text(
                (chat['name'] as String)[0],
                style: const TextStyle(color: AppTheme.champagne, fontWeight: FontWeight.bold, fontSize: 18),
              ),
            ),
            title: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    chat['name'] as String,
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                Text(chat['time'] as String, style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
              ],
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 4),
                Text(
                  chat['item'] as String,
                  style: const TextStyle(fontSize: 12, color: AppTheme.champagneDark, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 2),
                Text(
                  chat['lastMessage'] as String,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: unread > 0 ? AppTheme.textPrimary : AppTheme.textSecondary,
                    fontWeight: unread > 0 ? FontWeight.bold : FontWeight.normal,
                    fontSize: 13,
                  ),
                ),
              ],
            ),
            trailing: unread > 0
                ? Container(
                    padding: const EdgeInsets.all(6),
                    decoration: const BoxDecoration(
                      color: AppTheme.champagne,
                      shape: BoxShape.circle,
                    ),
                    child: Text(
                      unread.toString(),
                      style: const TextStyle(color: AppTheme.obsidian, fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  )
                : null,
            onTap: () {
              Navigator.of(context).push(
                MaterialPageRoute(
                  builder: (_) => ChatDetailScreen(
                    userName: chat['name'] as String,
                    itemTitle: chat['item'] as String,
                    initialPrice: chat['price'] as String,
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}
