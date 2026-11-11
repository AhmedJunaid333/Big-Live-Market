import 'dart:ui';
import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';

class ChatDetailScreen extends StatefulWidget {
  final String? userName;
  final String? contactName;
  final String itemTitle;
  final String? initialPrice;
  final int? conversationId;

  const ChatDetailScreen({
    super.key,
    this.userName,
    String? contactName,
    String? itemTitle,
    String? listingTitle,
    String? sellerName,
    this.initialPrice,
    this.conversationId,
  })  : itemTitle = listingTitle ?? (itemTitle ?? 'Listing'),
        contactName = sellerName ?? contactName;

  String get displayName => userName ?? contactName ?? 'Verified Seller';

  @override
  State<ChatDetailScreen> createState() => _ChatDetailScreenState();
}

class _ChatDetailScreenState extends State<ChatDetailScreen> {
  final _textController = TextEditingController();
  bool _isPlayingVoice = false;
  String? _activeOfferStatus; // 'accepted', 'countered', 'rejected'
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
    {
      'isMe': false,
      'isVoiceNote': true,
      'duration': '0:14',
      'text': 'Voice Note (0:14)',
      'time': '10:17 AM',
    },
  ];

  void _sendVoiceNote() {
    setState(() {
      _messages.add({
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
        content: Text('Audio voice message recorded & delivered!'),
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
          title: Text('Make Counter Offer', style: AppTheme.headlineMd),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Original Offer: PKR 4,200,000', style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted)),
              const SizedBox(height: 12),
              TextField(
                controller: counterController,
                keyboardType: TextInputType.number,
                style: AppTheme.headlineMd.copyWith(color: AppTheme.secondaryMint),
                decoration: InputDecoration(
                  labelText: 'Your Counter Amount (PKR)',
                  labelStyle: AppTheme.bodySm.copyWith(color: AppTheme.primaryLavender),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: Text('Cancel', style: TextStyle(color: AppTheme.onSurfaceMuted)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryLavender),
              onPressed: () {
                final amt = counterController.text.trim();
                Navigator.pop(ctx);
                setState(() {
                  _activeOfferStatus = 'countered';
                  _messages.add({
                    'isMe': true,
                    'isOffer': true,
                    'offerAmount': 'PKR $amt',
                    'text': 'Submitted Counter Offer: PKR $amt',
                    'time': 'Just now',
                  });
                });
              },
              child: const Text('SUBMIT COUNTER', style: TextStyle(color: AppTheme.backgroundDark, fontWeight: FontWeight.bold)),
            ),
          ],
        );
      },
    );
  }

  @override
  void dispose() {
    _textController.dispose();
    super.dispose();
  }

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

  @override
  Widget build(BuildContext context) {
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
                  color: AppTheme.backgroundMid.withValues(alpha: 0.8),
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
                      initials: widget.displayName[0],
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
                    NebulaBadgePill(
                      label: 'ONLINE',
                      color: AppTheme.secondaryMint,
                    ),
                  ],
                ),
              ),

              // Chat Messages
              Expanded(
                child: ListView.builder(
                  physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                  cacheExtent: 600,
                  padding: const EdgeInsets.all(AppTheme.gutter),
                  itemCount: _messages.length,
                  itemBuilder: (context, index) {
                    final msg = _messages[index];
                    final isMe = msg['isMe'] as bool;
                    final isOffer = (msg['isOffer'] ?? false) as bool;

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
                                    children: [
                                      NebulaBadgePill(
                                        label: _activeOfferStatus == 'accepted'
                                            ? 'OFFER ACCEPTED'
                                            : (_activeOfferStatus == 'countered'
                                                ? 'COUNTERED'
                                                : 'OFFER RECEIVED'),
                                        color: _activeOfferStatus == 'accepted'
                                            ? AppTheme.secondaryMint
                                            : AppTheme.accentAmber,
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
                                              'Voice Note (${msg['duration'] ?? '0:12'})',
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
                                if (isOffer && !isMe && _activeOfferStatus == null) ...[
                                  const SizedBox(height: 10),
                                  const Divider(color: AppTheme.borderHairline, height: 1),
                                  const SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Expanded(
                                        child: OutlinedButton(
                                          style: OutlinedButton.styleFrom(
                                            side: const BorderSide(color: AppTheme.secondaryMint),
                                            padding: const EdgeInsets.symmetric(vertical: 6),
                                          ),
                                          onPressed: () {
                                            setState(() => _activeOfferStatus = 'accepted');
                                            ScaffoldMessenger.of(context).showSnackBar(
                                              const SnackBar(
                                                backgroundColor: AppTheme.secondaryMint,
                                                content: Text('Offer accepted! Both parties notified.'),
                                              ),
                                            );
                                          },
                                          child: const Text('ACCEPT', style: TextStyle(color: AppTheme.secondaryMint, fontSize: 11, fontWeight: FontWeight.bold)),
                                        ),
                                      ),
                                      const SizedBox(width: 6),
                                      Expanded(
                                        child: ElevatedButton(
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: AppTheme.primaryLavender,
                                            padding: const EdgeInsets.symmetric(vertical: 6),
                                          ),
                                          onPressed: _makeCounterOffer,
                                          child: const Text('COUNTER', style: TextStyle(color: AppTheme.backgroundDark, fontSize: 11, fontWeight: FontWeight.bold)),
                                        ),
                                      ),
                                    ],
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
                            child: const Icon(
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
}
