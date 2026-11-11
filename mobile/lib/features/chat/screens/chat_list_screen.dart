import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
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
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic Header
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'CONVERSATIONS',
                      style: AppTheme.labelUppercase,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Direct Inquiries',
                      style: AppTheme.displayLg,
                    ),
                  ],
                ),
              ),

              // Chat Thread List
              Expanded(
                child: ListView.separated(
                  physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                  cacheExtent: 600,
                  padding: const EdgeInsets.fromLTRB(
                    AppTheme.gutter,
                    8,
                    AppTheme.gutter,
                    120, // Clear floating bottom tab bar
                  ),
                  itemCount: _threads.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final chat = _threads[index];
                    final unread = (chat['unread'] ?? 0) as int;

                    return RepaintBoundary(
                      child: NebulaGlassCard(
                      padding: const EdgeInsets.all(16),
                      borderRadius: AppTheme.radius2Xl,
                      onTap: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => ChatDetailScreen(
                              contactName: chat['name'] as String,
                              itemTitle: chat['item'] as String,
                            ),
                          ),
                        );
                      },
                      child: Row(
                        children: [
                          NebulaAvatar(
                            initials: (chat['name'] as String)[0],
                            size: 48,
                            isOnline: unread > 0,
                          ),
                          const SizedBox(width: 14),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Expanded(
                                      child: Text(
                                        chat['name'] as String,
                                        style: AppTheme.bodyMd.copyWith(
                                          fontWeight: FontWeight.w700,
                                          color: AppTheme.onSurface,
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    Text(
                                      chat['time'] as String,
                                      style: AppTheme.caption.copyWith(
                                        color: AppTheme.onSurfaceMuted,
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  chat['item'] as String,
                                  style: AppTheme.bodySm.copyWith(
                                    color: AppTheme.primaryLavender,
                                    fontWeight: FontWeight.w600,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Expanded(
                                      child: Text(
                                        chat['lastMessage'] as String,
                                        style: AppTheme.bodySm.copyWith(
                                          color: AppTheme.onSurfaceVariant,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    if (unread > 0)
                                      NebulaBadgePill(
                                        label: '$unread NEW',
                                        color: AppTheme.accentMint,
                                      ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
