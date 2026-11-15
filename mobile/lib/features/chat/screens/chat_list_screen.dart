import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../earn/providers/earn_provider.dart';
import '../../earn/widgets/sponsored_missions_sheet.dart';
import 'chat_detail_screen.dart';

class ChatListScreen extends ConsumerStatefulWidget {
  const ChatListScreen({super.key});

  @override
  ConsumerState<ChatListScreen> createState() => _ChatListScreenState();
}

class _ChatListScreenState extends ConsumerState<ChatListScreen> {
  final List<Map<String, dynamic>> _threads = [
    {
      'name': 'Big Live Rewards Official',
      'item': 'Sponsored Mission Payout',
      'price': 'PKR 150 Earned',
      'lastMessage': '🎉 Congratulations! Your mission reward of PKR 150 was credited to your wallet.',
      'time': 'Just now',
      'unread': 1,
      'isSystem': true,
    },
    {
      'name': 'Malik Tariq (Verified Seller)',
      'item': 'Toyota Land Cruiser V8 ZX',
      'price': 'PKR 38,500,000',
      'lastMessage': 'Sent an offer: PKR 37,000,000',
      'time': '10:16 AM',
      'unread': 2,
      'isSystem': false,
    },
    {
      'name': 'Usman Electronics',
      'item': 'iPhone 15 Pro Max 256GB Dual SIM',
      'price': 'PKR 365,000',
      'lastMessage': 'Yes, box and warranty card available.',
      'time': 'Yesterday',
      'unread': 0,
      'isSystem': false,
    },
    {
      'name': 'DHA Prime Properties',
      'item': '1 Kanal Luxury Designer Villa DHA 6',
      'price': 'PKR 85,000,000',
      'lastMessage': 'Can schedule a walkthrough this Saturday.',
      'time': 'Nov 8',
      'unread': 0,
      'isSystem': false,
    },
  ];

  void _openMissionsSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => SponsoredMissionsSheet(
        onMissionCompleted: (mission) {
          ref.read(earnProvider.notifier).claimMissionReward(mission);
          setState(() {
            _threads.insert(0, {
              'name': 'Big Live Rewards Official',
              'item': 'Task Reward Verified',
              'price': 'Rs. ${mission.rewardPkr} Credited',
              'lastMessage': '✅ Your task proof for "${mission.title}" was approved! PKR ${mission.rewardPkr} available in wallet.',
              'time': 'Just now',
              'unread': 1,
              'isSystem': true,
            });
          });
        },
      ),
    );
  }

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
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 12),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'MESSAGES & MISSIONS',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Direct Inquiries',
                          style: AppTheme.displayLg,
                        ),
                      ],
                    ),
                    // Quick Action Button to open Missions
                    GestureDetector(
                      onTap: _openMissionsSheet,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFFF59E0B), Color(0xFFD97706)],
                          ),
                          borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFFF59E0B).withValues(alpha: 0.3),
                              blurRadius: 8,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.stars_rounded, color: Colors.white, size: 16),
                            SizedBox(width: 5),
                            Text(
                              'Earn Rs',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Sponsored Earn / Daily Missions Action Banner (Message Section Feature)
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 0, AppTheme.gutter, 14),
                child: GestureDetector(
                  onTap: _openMissionsSheet,
                  child: Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          const Color(0xFF1E293B),
                          const Color(0xFF0F172A).withValues(alpha: 0.9),
                        ],
                      ),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: const Color(0xFFF59E0B).withValues(alpha: 0.4),
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFFF59E0B).withValues(alpha: 0.1),
                          blurRadius: 12,
                        ),
                      ],
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF59E0B).withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(
                            Icons.monetization_on_rounded,
                            color: Color(0xFFFBBF24),
                            size: 24,
                          ),
                        ),
                        const SizedBox(width: 12),
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  Text(
                                    'Daily Earning Missions',
                                    style: TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.white,
                                    ),
                                  ),
                                  SizedBox(width: 6),
                                  Text(
                                    'HOT',
                                    style: TextStyle(
                                      fontSize: 9,
                                      fontWeight: FontWeight.w900,
                                      color: Color(0xFFFBBF24),
                                    ),
                                  ),
                                ],
                              ),
                              SizedBox(height: 2),
                              Text(
                                'Watch partner ads & complete surveys to earn PKR daily',
                                style: TextStyle(
                                  fontSize: 11,
                                  color: Color(0xFF94A3B8),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const Icon(
                          Icons.arrow_forward_ios_rounded,
                          color: Color(0xFFFBBF24),
                          size: 14,
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              // Chat Thread List
              Expanded(
                child: ListView.separated(
                  physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                  cacheExtent: 600,
                  padding: const EdgeInsets.fromLTRB(
                    AppTheme.gutter,
                    0,
                    AppTheme.gutter,
                    120, // Clear floating bottom tab bar
                  ),
                  itemCount: _threads.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final chat = _threads[index];
                    final unread = (chat['unread'] ?? 0) as int;
                    final isSystem = chat['isSystem'] == true;

                    return RepaintBoundary(
                      child: NebulaGlassCard(
                        padding: const EdgeInsets.all(16),
                        borderRadius: AppTheme.radius2Xl,
                        onTap: () {
                          if (isSystem) {
                            _openMissionsSheet();
                          } else {
                            Navigator.of(context).push(
                              MaterialPageRoute(
                                builder: (_) => ChatDetailScreen(
                                  contactName: chat['name'] as String,
                                  itemTitle: chat['item'] as String,
                                ),
                              ),
                            );
                          }
                        },
                        child: Row(
                          children: [
                            if (isSystem)
                              Container(
                                width: 48,
                                height: 48,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  gradient: const LinearGradient(
                                    colors: [Color(0xFFF59E0B), Color(0xFFD97706)],
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: const Color(0xFFF59E0B).withValues(alpha: 0.3),
                                      blurRadius: 8,
                                    ),
                                  ],
                                ),
                                child: const Icon(
                                  Icons.stars_rounded,
                                  color: Colors.white,
                                  size: 26,
                                ),
                              )
                            else
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
                                            color: isSystem ? const Color(0xFFFBBF24) : AppTheme.onSurface,
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
                                      color: isSystem ? const Color(0xFF34D399) : AppTheme.primaryLavender,
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
                                          text: isSystem ? 'REWARD' : '$unread NEW',
                                          tone: isSystem ? NebulaBadgeTone.lavender : NebulaBadgeTone.mint,
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
