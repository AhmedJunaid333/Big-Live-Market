import 'dart:ui';
import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';

class ListingDetailScreen extends StatelessWidget {
  final Map<String, dynamic> listing;

  const ListingDetailScreen({super.key, required this.listing});

  @override
  Widget build(BuildContext context) {
    final title = listing['title']?.toString() ?? 'Advertisement';
    final description = listing['description']?.toString() ?? 'No description provided.';
    final priceMinor = (listing['price_minor'] ?? 0) as num;
    final currency = listing['currency']?.toString() ?? 'PKR';
    final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';
    final condition = listing['condition_code']?.toString() ?? 'used';
    final isNew = condition.toLowerCase() == 'new';
    final location = listing['location_label']?.toString() ?? 'Pakistan';
    final seller = listing['seller'] as Map<String, dynamic>?;
    final sellerName = seller?['name']?.toString() ?? 'Verified Member';
    final sellerPhone = seller?['phone_e164']?.toString() ?? '+92 300 1234567';

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: Stack(
          children: [
            // Scrollable Content
            CustomScrollView(
              slivers: [
                // Glass App Bar
                SliverAppBar(
                  pinned: true,
                  backgroundColor: AppTheme.backgroundMid.withValues(alpha: 0.8),
                  surfaceTintColor: Colors.transparent,
                  elevation: 0,
                  leading: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      color: AppTheme.onSurface,
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                  ),
                  title: Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: AppTheme.headlineMd.copyWith(color: AppTheme.onSurface),
                  ),
                  actions: [
                    NebulaGlassIconButton(
                      icon: Icons.share_rounded,
                      color: AppTheme.onSurfaceVariant,
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            backgroundColor: AppTheme.backgroundMid,
                            content: Text(
                              'Listing link copied to clipboard!',
                              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(width: 8),
                    NebulaGlassIconButton(
                      icon: Icons.favorite_border_rounded,
                      color: AppTheme.accentPink,
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            backgroundColor: AppTheme.backgroundMid,
                            content: Text(
                              'Saved to Favorites!',
                              style: AppTheme.bodyMd.copyWith(color: AppTheme.accentPink),
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(width: AppTheme.gutter),
                  ],
                ),

                // Main Details Body
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(
                      AppTheme.gutter,
                      16,
                      AppTheme.gutter,
                      120, // Clear bottom action bar
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        // Hero Image Container with Glass Overlay
                        Container(
                          height: 260,
                          decoration: BoxDecoration(
                            color: AppTheme.backgroundMid,
                            borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
                            border: Border.all(color: AppTheme.borderHairline),
                            boxShadow: AppTheme.glassShadow,
                          ),
                          child: Stack(
                            alignment: Alignment.center,
                            children: [
                              Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  TintedIconBubble(
                                    icon: Icons.photo_library_rounded,
                                    accentColor: AppTheme.primaryLavender,
                                    size: 64,
                                    iconSize: 32,
                                  ),
                                  const SizedBox(height: 12),
                                  Text(
                                    'Verified HD Media',
                                    style: AppTheme.bodySm.copyWith(
                                      color: AppTheme.onSurfaceVariant,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ],
                              ),
                              Positioned(
                                bottom: 16,
                                left: 16,
                                child: NebulaBadgePill(
                                  label: condition,
                                  color: isNew ? AppTheme.accentMint : AppTheme.accentAmber,
                                ),
                              ),
                              Positioned(
                                bottom: 16,
                                right: 16,
                                child: NebulaBadgePill(
                                  label: 'ID VERIFIED',
                                  color: AppTheme.secondaryMint,
                                ),
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Price & Title Glass Card
                        NebulaGlassCard(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'PRICE',
                                style: AppTheme.labelUppercase,
                              ),
                              const SizedBox(height: 4),
                              Text(
                                formattedPrice,
                                style: AppTheme.numeralXl.copyWith(
                                  color: AppTheme.primaryLavender,
                                  fontSize: 32,
                                  shadows: const [
                                    Shadow(
                                      color: Color(0x66D8B4FE),
                                      blurRadius: 16,
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(height: 12),
                              Text(
                                title,
                                style: AppTheme.headlineMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Row(
                                children: [
                                  const Icon(
                                    Icons.location_on_rounded,
                                    size: 16,
                                    color: AppTheme.onSurfaceMuted,
                                  ),
                                  const SizedBox(width: 4),
                                  Expanded(
                                    child: Text(
                                      location,
                                      style: AppTheme.bodySm.copyWith(
                                        color: AppTheme.onSurfaceVariant,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Seller Glass Card
                        NebulaGlassCard(
                          child: Row(
                            children: [
                              NebulaAvatar(
                                initials: sellerName.isNotEmpty ? sellerName[0].toUpperCase() : 'S',
                                size: 52,
                                isOnline: true,
                              ),
                              const SizedBox(width: 16),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      sellerName,
                                      style: AppTheme.bodyMd.copyWith(
                                        fontWeight: FontWeight.w700,
                                        color: AppTheme.onSurface,
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      'Member since 2026 · Response: 100%',
                                      style: AppTheme.caption.copyWith(
                                        color: AppTheme.onSurfaceMuted,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              NebulaBadgePill(
                                label: 'PRO',
                                color: AppTheme.accentPink,
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Overview & Highlights Glass Card
                        NebulaGlassCard(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    'OVERVIEW & HIGHLIGHTS',
                                    style: AppTheme.labelUppercase,
                                  ),
                                  const Icon(
                                    Icons.info_outline_rounded,
                                    size: 16,
                                    color: AppTheme.onSurfaceMuted,
                                  ),
                                ],
                              ),
                              const SizedBox(height: 12),
                              Text(
                                description,
                                style: AppTheme.bodyMd.copyWith(
                                  color: AppTheme.onSurfaceVariant,
                                  height: 1.6,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),

            // Floating Bottom Glass Action Bar
            Positioned(
              left: 0,
              right: 0,
              bottom: 0,
              child: ClipRRect(
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: AppTheme.gutter,
                      vertical: 16,
                    ),
                    decoration: BoxDecoration(
                      color: AppTheme.backgroundMid.withValues(alpha: 0.85),
                      border: const Border(
                        top: BorderSide(color: AppTheme.borderHairline, width: 1),
                      ),
                    ),
                    child: SafeArea(
                      top: false,
                      child: Row(
                        children: [
                          Expanded(
                            flex: 2,
                            child: NebulaButtonSecondary(
                              label: 'Call Seller',
                              icon: Icons.phone_rounded,
                              onPressed: () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    backgroundColor: AppTheme.backgroundMid,
                                    content: Text('Calling $sellerPhone...'),
                                  ),
                                );
                              },
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            flex: 3,
                            child: NebulaButtonPrimary(
                              label: 'Direct Chat',
                              icon: const Icon(
                                Icons.chat_bubble_rounded,
                                size: 20,
                                color: AppTheme.onPrimary,
                              ),
                              onPressed: () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    backgroundColor: AppTheme.backgroundMid,
                                    content: Text('Starting chat with $sellerName...'),
                                  ),
                                );
                              },
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
