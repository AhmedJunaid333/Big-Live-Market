import 'dart:ui';
import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';

class ChatDetailScreen extends StatefulWidget {
  final String? userName;
  final String? contactName;
  final String itemTitle;
  final String? initialPrice;

  const ChatDetailScreen({
    super.key,
    this.userName,
    this.contactName,
    required this.itemTitle,
    this.initialPrice,
  });

  String get displayName => userName ?? contactName ?? 'Verified Seller';

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
                  padding: const EdgeInsets.all(AppTheme.gutter),
                  itemCount: _messages.length,
                  itemBuilder: (context, index) {
                    final msg = _messages[index];
                    final isMe = msg['isMe'] as bool;
                    final isOffer = (msg['isOffer'] ?? false) as bool;

                    return Align(
                      alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        constraints: BoxConstraints(
                          maxWidth: MediaQuery.of(context).size.width * 0.78,
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.only(
                            topLeft: const Radius.circular(20),
                            topRight: const Radius.circular(20),
                            bottomLeft: Radius.circular(isMe ? 20 : 4),
                            bottomRight: Radius.circular(isMe ? 4 : 20),
                          ),
                          child: BackdropFilter(
                            filter: ImageFilter.blur(sigmaX: 8, sigmaY: 8),
                            child: Container(
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: isMe
                                    ? AppTheme.primaryLavender.withValues(alpha: 0.18)
                                    : AppTheme.surfaceGlass,
                                borderRadius: BorderRadius.only(
                                  topLeft: const Radius.circular(20),
                                  topRight: const Radius.circular(20),
                                  bottomLeft: Radius.circular(isMe ? 20 : 4),
                                  bottomRight: Radius.circular(isMe ? 4 : 20),
                                ),
                                border: Border.all(
                                  color: isMe
                                      ? AppTheme.primaryLavender.withValues(alpha: 0.35)
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
                                          label: 'OFFER RECEIVED',
                                          color: AppTheme.accentAmber,
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 6),
                                  ],
                                  Text(
                                    msg['text'] as String,
                                    style: AppTheme.bodyMd.copyWith(
                                      color: AppTheme.onSurface,
                                    ),
                                  ),
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
