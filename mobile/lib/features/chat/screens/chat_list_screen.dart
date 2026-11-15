import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../../earn/providers/earn_provider.dart';
import '../../earn/widgets/sponsored_missions_sheet.dart';
import '../providers/chat_provider.dart';
import 'chat_detail_screen.dart';

class ChatListScreen extends ConsumerStatefulWidget {
  const ChatListScreen({super.key});

  @override
  ConsumerState<ChatListScreen> createState() => _ChatListScreenState();
}

class _ChatListScreenState extends ConsumerState<ChatListScreen> {
  void _openMissionsSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => SponsoredMissionsSheet(
        onMissionCompleted: (mission) {
          ref.read(earnProvider.notifier).claimMissionReward(mission);
        },
      ),
    );
  }

  String _formatTime(dynamic rawDate) {
    if (rawDate == null) return 'Active';
    try {
      final dt = DateTime.parse(rawDate.toString()).toLocal();
      final now = DateTime.now();
      final diff = now.difference(dt);
      if (diff.inMinutes < 2) return 'Just now';
      if (diff.inHours < 1) return '${diff.inMinutes}m ago';
      if (diff.inDays < 1) {
        final hour = dt.hour % 12 == 0 ? 12 : dt.hour % 12;
        final ampm = dt.hour >= 12 ? 'PM' : 'AM';
        final min = dt.minute.toString().padLeft(2, '0');
        return '$hour:$min $ampm';
      }
      if (diff.inDays < 7) return '${diff.inDays}d ago';
      return '${dt.day}/${dt.month}';
    } catch (_) {
      return 'Active';
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final currentUserId = authState.user?.id;
    final conversationsAsync = ref.watch(conversationsProvider);

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
                          style: AppTheme.labelUppercase.copyWith(
                            letterSpacing: 2.2,
                            color: AppTheme.primaryLavender,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'Secure Marketplace In-App Chat',
                          style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                        ),
                      ],
                    ),
                    IconButton(
                      icon: const Icon(Icons.refresh_rounded, color: AppTheme.primaryLavender),
                      tooltip: 'Refresh conversations',
                      onPressed: () => ref.invalidate(conversationsProvider),
                    ),
                  ],
                ),
              ),

              // Sponsored Mission & Payout Banner
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 0, AppTheme.gutter, 16),
                child: GestureDetector(
                  onTap: _openMissionsSheet,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0x33F59E0B), Color(0x11D97706)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: const Color(0xFFF59E0B).withValues(alpha: 0.35),
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 40,
                          height: 40,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: const LinearGradient(
                              colors: [Color(0xFFF59E0B), Color(0xFFD97706)],
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFFF59E0B).withValues(alpha: 0.4),
                                blurRadius: 10,
                              ),
                            ],
                          ),
                          child: const Icon(
                            Icons.stars_rounded,
                            color: Colors.white,
                            size: 22,
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
                                      fontSize: 13,
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
                child: RefreshIndicator(
                  color: AppTheme.primaryLavender,
                  onRefresh: () async => ref.invalidate(conversationsProvider),
                  child: conversationsAsync.when(
                    data: (conversations) {
                      if (conversations.isEmpty) {
                        return ListView(
                          physics: const AlwaysScrollableScrollPhysics(),
                          padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 48),
                          children: [
                            Center(
                              child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Container(
                                    padding: const EdgeInsets.all(20),
                                    decoration: BoxDecoration(
                                      shape: BoxShape.circle,
                                      color: AppTheme.primaryLavender.withValues(alpha: 0.1),
                                      border: Border.all(color: AppTheme.primaryLavender.withValues(alpha: 0.3)),
                                    ),
                                    child: const Icon(
                                      Icons.chat_bubble_outline_rounded,
                                      size: 48,
                                      color: AppTheme.primaryLavender,
                                    ),
                                  ),
                                  const SizedBox(height: 18),
                                  Text(
                                    'No Active Conversations',
                                    style: AppTheme.headlineMd.copyWith(color: Colors.white),
                                  ),
                                  const SizedBox(height: 8),
                                  Padding(
                                    padding: const EdgeInsets.symmetric(horizontal: 24),
                                    child: Text(
                                      'Browse marketplace ads and tap "Direct Chat" to start negotiating directly with verified sellers.',
                                      textAlign: TextAlign.center,
                                      style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        );
                      }

                      return ListView.separated(
                        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                        cacheExtent: 600,
                        padding: const EdgeInsets.fromLTRB(
                          AppTheme.gutter,
                          0,
                          AppTheme.gutter,
                          120, // Clear floating bottom tab bar
                        ),
                        itemCount: conversations.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 12),
                        itemBuilder: (context, index) {
                          final conv = conversations[index] as Map<String, dynamic>;
                          final convId = conv['id'] as int;
                          final listing = conv['listing'] as Map<String, dynamic>?;
                          final buyer = conv['buyer'] as Map<String, dynamic>?;
                          final seller = conv['seller'] as Map<String, dynamic>?;

                          // Determine conversation partner name
                          final bool isBuyer = currentUserId != null && conv['buyer_id'] == currentUserId;
                          final String otherPartyName = isBuyer
                              ? (seller?['name']?.toString() ?? 'Seller')
                              : (buyer?['name']?.toString() ?? 'Buyer');

                          final String listingTitle = listing?['title']?.toString() ?? 'Marketplace Item';
                          final int unread = (conv['unread_count'] ?? 0) as int;

                          // Extract latest message
                          String lastMessageText = 'Conversation started';
                          final messagesList = conv['messages'] as List<dynamic>?;
                          if (messagesList != null && messagesList.isNotEmpty) {
                            final latestMsg = messagesList.first as Map<String, dynamic>;
                            lastMessageText = latestMsg['body']?.toString() ?? 'Message';
                          }

                          final timeLabel = _formatTime(conv['last_message_at'] ?? conv['updated_at']);

                          return RepaintBoundary(
                            child: NebulaGlassCard(
                              padding: const EdgeInsets.all(16),
                              borderRadius: AppTheme.radius2Xl,
                              onTap: () {
                                Navigator.of(context).push(
                                  MaterialPageRoute(
                                    builder: (_) => ChatDetailScreen(
                                      conversationId: convId,
                                      contactName: otherPartyName,
                                      itemTitle: listingTitle,
                                      initialPrice: listing != null && listing['price_minor'] != null
                                          ? 'PKR ${(listing['price_minor'] / 100).toStringAsFixed(0)}'
                                          : null,
                                    ),
                                  ),
                                ).then((_) => ref.invalidate(conversationsProvider));
                              },
                              child: Row(
                                children: [
                                  NebulaAvatar(
                                    initials: otherPartyName.isNotEmpty ? otherPartyName[0] : 'U',
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
                                                otherPartyName,
                                                style: AppTheme.bodyMd.copyWith(
                                                  fontWeight: FontWeight.w700,
                                                  color: AppTheme.onSurface,
                                                ),
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                            Text(
                                              timeLabel,
                                              style: AppTheme.caption.copyWith(
                                                color: AppTheme.onSurfaceMuted,
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          listingTitle,
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
                                                lastMessageText,
                                                style: AppTheme.bodySm.copyWith(
                                                  color: AppTheme.onSurfaceVariant,
                                                ),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                            if (unread > 0)
                                              NebulaBadgePill(
                                                text: '$unread NEW',
                                                tone: NebulaBadgeTone.mint,
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
                      );
                    },
                    loading: () => const Center(
                      child: CircularProgressIndicator(color: AppTheme.primaryLavender),
                    ),
                    error: (err, _) => Center(
                      child: Padding(
                        padding: const EdgeInsets.all(AppTheme.gutter),
                        child: Text(
                          'Failed to load chats: $err',
                          style: AppTheme.bodySm.copyWith(color: AppTheme.accentRose),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
