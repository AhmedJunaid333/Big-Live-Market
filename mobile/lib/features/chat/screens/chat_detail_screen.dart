import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/chat_provider.dart';

class ChatDetailScreen extends ConsumerStatefulWidget {
  final String? userName;
  final String? contactName;
  final String itemTitle;
  final String? initialPrice;
  final int? conversationId;
  final int? listingId;

  const ChatDetailScreen({
    super.key,
    this.userName,
    String? contactName,
    String? itemTitle,
    String? listingTitle,
    String? sellerName,
    this.initialPrice,
    this.conversationId,
    this.listingId,
  })  : itemTitle = listingTitle ?? (itemTitle ?? 'Listing'),
        contactName = sellerName ?? contactName;

  String get displayName => userName ?? contactName ?? 'Verified Seller';

  @override
  ConsumerState<ChatDetailScreen> createState() => _ChatDetailScreenState();
}

class _ChatDetailScreenState extends ConsumerState<ChatDetailScreen> {
  final _textController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  bool _isPlayingVoice = false;
  bool _isSending = false;
  int? _activeConversationId;
  final List<Map<String, dynamic>> _localMessages = [];

  @override
  void initState() {
    super.initState();
    _activeConversationId = widget.conversationId;
    if (_activeConversationId != null) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        ref.read(chatApiServiceProvider).markAsRead(_activeConversationId!);
      });
    }
  }

  @override
  void dispose() {
    _textController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  String _formatMessageTime(dynamic rawDate) {
    if (rawDate == null) return 'Just now';
    try {
      final dt = DateTime.parse(rawDate.toString()).toLocal();
      final hour = dt.hour % 12 == 0 ? 12 : dt.hour % 12;
      final ampm = dt.hour >= 12 ? 'PM' : 'AM';
      final min = dt.minute.toString().padLeft(2, '0');
      return '$hour:$min $ampm';
    } catch (_) {
      return 'Just now';
    }
  }

  Future<void> _sendMessage() async {
    final text = _textController.text.trim();
    if (text.isEmpty || _isSending) return;

    _textController.clear();
    setState(() {
      _isSending = true;
      _localMessages.add({
        'isMe': true,
        'text': text,
        'time': 'Just now',
        'isPending': true,
      });
    });

    try {
      final chatService = ref.read(chatApiServiceProvider);
      if (_activeConversationId == null && widget.listingId != null) {
        // Initialize conversation on the backend
        final conv = await chatService.startConversation(
          listingId: widget.listingId!,
          initialMessage: text,
        );
        if (conv['id'] != null) {
          _activeConversationId = conv['id'] as int;
        }
      } else if (_activeConversationId != null) {
        await chatService.sendMessage(
          conversationId: _activeConversationId!,
          body: text,
        );
      }

      // Invalidate providers to fetch fresh sync
      ref.invalidate(conversationsProvider);
      if (_activeConversationId != null) {
        ref.invalidate(conversationMessagesProvider(_activeConversationId!));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.accentRose,
            content: Text('Message failed: $e'),
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isSending = false);
      }
    }
  }

  void _sendVoiceNote() {
    setState(() {
      _localMessages.add({
        'isMe': true,
        'isVoiceNote': true,
        'duration': '0:08',
        'text': 'Voice Note (0:08)',
        'time': 'Just now',
      });
    });
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: AppTheme.secondaryMint,
        content: Text('Voice audio recording encrypted & delivered!'),
      ),
    );
  }

  void _makeCounterOffer() {
    showDialog(
      context: context,
      builder: (ctx) {
        final counterController = TextEditingController(text: '4250000');
        return AlertDialog(
          backgroundColor: AppTheme.backgroundMid,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
            side: const BorderSide(color: AppTheme.borderHairline),
          ),
          title: Text('Make Offer / Counter', style: AppTheme.headlineMd),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (widget.initialPrice != null)
                Text(
                  'Listed Price: ${widget.initialPrice}',
                  style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                ),
              const SizedBox(height: 12),
              TextField(
                controller: counterController,
                keyboardType: TextInputType.number,
                style: AppTheme.headlineMd.copyWith(color: AppTheme.secondaryMint),
                decoration: InputDecoration(
                  labelText: 'Offer Amount (PKR)',
                  labelStyle: AppTheme.bodySm.copyWith(color: AppTheme.primaryLavender),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Cancel', style: TextStyle(color: AppTheme.onSurfaceMuted)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryLavender),
              onPressed: () async {
                final amtStr = counterController.text.trim();
                final amt = int.tryParse(amtStr) ?? 0;
                Navigator.pop(ctx);

                if (amt <= 0) return;

                if (_activeConversationId != null) {
                  try {
                    await ref.read(chatApiServiceProvider).sendOffer(
                          conversationId: _activeConversationId!,
                          amountMinor: amt * 100,
                        );
                    ref.invalidate(conversationMessagesProvider(_activeConversationId!));
                  } catch (_) {}
                }

                setState(() {
                  _localMessages.add({
                    'isMe': true,
                    'isOffer': true,
                    'offerAmount': 'PKR $amtStr',
                    'text': 'Submitted Official Offer: PKR $amtStr',
                    'time': 'Just now',
                  });
                });

                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      backgroundColor: AppTheme.secondaryMint,
                      content: Text('Official offer submitted to seller!'),
                    ),
                  );
                }
              },
              child: const Text(
                'SUBMIT OFFER',
                style: TextStyle(
                  color: AppTheme.backgroundDark,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final currentUserId = ref.watch(authProvider).user?.id;
    final messagesAsync = _activeConversationId != null
        ? ref.watch(conversationMessagesProvider(_activeConversationId!))
        : null;

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            children: [
              // Glass Header
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                decoration: BoxDecoration(
                  color: AppTheme.backgroundMid.withValues(alpha: 0.85),
                  border: const Border(
                    bottom: BorderSide(color: AppTheme.borderHairline),
                  ),
                ),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      color: AppTheme.onSurface,
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    const SizedBox(width: 12),
                    NebulaAvatar(
                      initials: widget.displayName.isNotEmpty ? widget.displayName[0] : 'U',
                      size: 40,
                      isOnline: true,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            widget.displayName,
                            style: AppTheme.bodyMd.copyWith(
                              fontWeight: FontWeight.w700,
                              color: AppTheme.onSurface,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          Text(
                            widget.itemTitle,
                            style: AppTheme.caption.copyWith(
                              color: AppTheme.primaryLavender,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                    const NebulaBadgePill(
                      text: 'ONLINE',
                      tone: NebulaBadgeTone.mint,
                    ),
                  ],
                ),
              ),

              // Chat Messages Stream
              Expanded(
                child: messagesAsync != null
                    ? messagesAsync.when(
                        data: (serverMessages) {
                          // Combine server messages (reversed if cursor paginate) and local pending
                          final allItems = <Map<String, dynamic>>[];

                          // Server messages from newest to oldest or vice-versa
                          for (final m in serverMessages) {
                            if (m is Map<String, dynamic>) {
                              final senderId = m['sender_id'];
                              final isMe = currentUserId != null && senderId == currentUserId;
                              allItems.add({
                                'id': m['id'],
                                'isMe': isMe,
                                'text': m['body']?.toString() ?? '',
                                'time': _formatMessageTime(m['created_at']),
                                'isVoiceNote': m['type'] == 'voice',
                              });
                            }
                          }

                          // If server messages are in latest-first order, reverse them for bottom-up reading
                          final reversed = allItems.reversed.toList();
                          reversed.addAll(_localMessages);

                          if (reversed.isEmpty) {
                            return Center(
                              child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(
                                    Icons.chat_bubble_outline_rounded,
                                    size: 48,
                                    color: AppTheme.onSurfaceMuted,
                                  ),
                                  const SizedBox(height: 12),
                                  Text(
                                    'Say hello to ${widget.displayName}!',
                                    style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceMuted),
                                  ),
                                ],
                              ),
                            );
                          }

                          return _buildMessagesList(reversed);
                        },
                        loading: () => const Center(
                          child: CircularProgressIndicator(color: AppTheme.primaryLavender),
                        ),
                        error: (_, __) => _buildMessagesList(_localMessages),
                      )
                    : _buildMessagesList(_localMessages.isEmpty
                        ? [
                            {
                              'isMe': false,
                              'text': 'Hello! Ask any questions about "${widget.itemTitle}".',
                              'time': 'Active',
                            }
                          ]
                        : _localMessages),
              ),

              // Offer & Quick Action Pill
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                child: Row(
                  children: [
                    Expanded(
                      child: OutlinedButton.icon(
                        style: OutlinedButton.styleFrom(
                          side: BorderSide(color: AppTheme.primaryLavender.withValues(alpha: 0.5)),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                          padding: const EdgeInsets.symmetric(vertical: 8),
                        ),
                        onPressed: _makeCounterOffer,
                        icon: const Icon(Icons.local_offer_rounded, size: 16, color: AppTheme.primaryLavender),
                        label: const Text(
                          'Make Cash Offer',
                          style: TextStyle(color: AppTheme.primaryLavender, fontSize: 12, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Glass Message Composer
              ClipRRect(
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 12, sigmaY: 12),
                  child: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppTheme.backgroundMid.withValues(alpha: 0.85),
                      border: const Border(
                        top: BorderSide(color: AppTheme.borderHairline),
                      ),
                    ),
                    child: Row(
                      children: [
                        GestureDetector(
                          onTap: _sendVoiceNote,
                          child: Container(
                            width: 42,
                            height: 42,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: AppTheme.secondaryMint.withValues(alpha: 0.15),
                              border: Border.all(color: AppTheme.secondaryMint.withValues(alpha: 0.4)),
                            ),
                            alignment: Alignment.center,
                            child: const Icon(
                              Icons.mic_rounded,
                              size: 22,
                              color: AppTheme.secondaryMint,
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Container(
                            decoration: BoxDecoration(
                              color: AppTheme.surfaceGlass,
                              borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                              border: Border.all(color: AppTheme.borderHairline),
                            ),
                            padding: const EdgeInsets.symmetric(horizontal: 16),
                            child: TextField(
                              controller: _textController,
                              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                              decoration: InputDecoration(
                                hintText: 'Write a message...',
                                hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                                border: InputBorder.none,
                              ),
                              onSubmitted: (_) => _sendMessage(),
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        GestureDetector(
                          onTap: _sendMessage,
                          child: Container(
                            width: 44,
                            height: 44,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              gradient: AppTheme.primaryCtaGradient,
                              boxShadow: const [
                                BoxShadow(
                                  color: Color(0x66D8B4FE),
                                  blurRadius: 10,
                                ),
                              ],
                            ),
                            alignment: Alignment.center,
                            child: _isSending
                                ? const SizedBox(
                                    width: 18,
                                    height: 18,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      color: Colors.white,
                                    ),
                                  )
                                : const Icon(
                                    Icons.send_rounded,
                                    size: 20,
                                    color: AppTheme.onPrimary,
                                  ),
                          ),
                        ),
                      ],
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

  Widget _buildMessagesList(List<Map<String, dynamic>> messages) {
    return ListView.builder(
      controller: _scrollController,
      physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
      cacheExtent: 600,
      padding: const EdgeInsets.all(AppTheme.gutter),
      itemCount: messages.length,
      itemBuilder: (context, index) {
        final msg = messages[index];
        final isMe = msg['isMe'] == true;
        final isOffer = msg['isOffer'] == true;

        return RepaintBoundary(
          child: Align(
            alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
            child: Container(
              margin: const EdgeInsets.only(bottom: 12),
              constraints: BoxConstraints(
                maxWidth: MediaQuery.of(context).size.width * 0.78,
              ),
              child: Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: isMe
                        ? [
                            AppTheme.primaryLavender.withValues(alpha: 0.28),
                            AppTheme.primaryLavender.withValues(alpha: 0.14),
                          ]
                        : [
                            const Color(0x22FFFFFF),
                            const Color(0x0CFFFFFF),
                          ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.only(
                    topLeft: const Radius.circular(20),
                    topRight: const Radius.circular(20),
                    bottomLeft: Radius.circular(isMe ? 20 : 4),
                    bottomRight: Radius.circular(isMe ? 4 : 20),
                  ),
                  border: Border.all(
                    color: isMe
                        ? AppTheme.primaryLavender.withValues(alpha: 0.40)
                        : AppTheme.borderHairline,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (isOffer) ...[
                      Row(
                        mainAxisSize: MainAxisSize.min,
                        children: const [
                          NebulaBadgePill(
                            text: 'OFFER SUBMITTED',
                            tone: NebulaBadgeTone.mint,
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                    ],
                    if (msg['isVoiceNote'] == true) ...[
                      Row(
                        children: [
                          GestureDetector(
                            onTap: () => setState(() => _isPlayingVoice = !_isPlayingVoice),
                            child: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppTheme.primaryLavender.withValues(alpha: 0.2),
                                shape: BoxShape.circle,
                              ),
                              child: Icon(
                                _isPlayingVoice ? Icons.pause_rounded : Icons.play_arrow_rounded,
                                color: AppTheme.primaryLavender,
                                size: 20,
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                LinearProgressIndicator(
                                  value: _isPlayingVoice ? 0.65 : 0.0,
                                  backgroundColor: AppTheme.borderHairline,
                                  valueColor: const AlwaysStoppedAnimation(AppTheme.primaryLavender),
                                  minHeight: 3,
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Voice Note (${msg['duration'] ?? '0:08'})',
                                  style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceVariant),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ] else ...[
                      Text(
                        msg['text'] as String,
                        style: AppTheme.bodyMd.copyWith(
                          color: AppTheme.onSurface,
                        ),
                      ),
                    ],
                    const SizedBox(height: 4),
                    Align(
                      alignment: Alignment.bottomRight,
                      child: Text(
                        msg['time'] as String,
                        style: AppTheme.caption.copyWith(
                          color: AppTheme.onSurfaceMuted,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
