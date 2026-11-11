import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:share_plus/share_plus.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import '../../chat/screens/chat_detail_screen.dart';
import '../providers/favorites_provider.dart';
import '../widgets/nextgen_marketplace_widgets.dart';

class ListingDetailScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic>? listing;
  final int? listingId;

  const ListingDetailScreen({
    super.key,
    this.listing,
    this.listingId,
  }) : assert(listing != null || listingId != null, 'Either listing or listingId must be provided');

  @override
  ConsumerState<ListingDetailScreen> createState() => _ListingDetailScreenState();
}

class _ListingDetailScreenState extends ConsumerState<ListingDetailScreen> {
  Map<String, dynamic>? _fetchedListing;
  bool _isLoading = false;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    if (widget.listing == null && widget.listingId != null) {
      _loadListingDetails();
    }
  }

  Future<void> _loadListingDetails() async {
    setState(() => _isLoading = true);
    try {
      final client = ref.read(apiClientProvider);
      final res = await client.dio.get('/listings/${widget.listingId}');
      final data = res.data['data'];
      if (data is Map<String, dynamic>) {
        setState(() => _fetchedListing = data);
      }
    } catch (e) {
      setState(() => _errorMessage = e.toString());
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  Map<String, dynamic> get _activeListing => widget.listing ?? _fetchedListing ?? {};

  Future<void> _callSeller(String phone) async {
    final cleanPhone = phone.replaceAll(RegExp(r'[^0-9+]'), '');
    final uri = Uri.parse('tel:$cleanPhone');
    try {
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      } else {
        await launchUrl(uri);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.backgroundMid,
            content: Text('Could not open phone dialer: $e'),
          ),
        );
      }
    }
  }

  void _shareListing(String title, String price, int id) {
    // ignore: deprecated_member_use
    Share.share(
      'Check out "$title" for $price on Big Live Marketplace:\nhttps://biglive.pk/listings/$id',
      subject: title,
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: AppTheme.backgroundDark,
        body: const NebulaAtmosphericBackground(
          child: Center(
            child: CircularProgressIndicator(color: AppTheme.primaryLavender),
          ),
        ),
      );
    }

    if (_errorMessage != null && widget.listing == null) {
      return Scaffold(
        backgroundColor: AppTheme.backgroundDark,
        body: NebulaAtmosphericBackground(
          child: SafeArea(
            child: Center(
              child: Padding(
                padding: const EdgeInsets.all(AppTheme.gutter),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      onPressed: () => Navigator.pop(context),
                    ),
                    const SizedBox(height: 16),
                    Text('Failed to load listing', style: AppTheme.headlineMd),
                    const SizedBox(height: 8),
                    Text(_errorMessage!, style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant)),
                  ],
                ),
              ),
            ),
          ),
        ),
      );
    }

    final listing = _activeListing;
    final id = (listing['id'] as num?)?.toInt() ?? widget.listingId ?? 0;
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
    final sellerPhone = seller?['phone_e164']?.toString() ?? '+923001234567';

    final isFav = ref.watch(favoritesProvider).contains(id);

    return Scaffold(
      backgroundColor: AppTheme.backgroundDark,
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
                      onPressed: () => _shareListing(title, formattedPrice, id),
                    ),
                    const SizedBox(width: 8),
                    NebulaGlassIconButton(
                      icon: isFav ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                      color: isFav ? AppTheme.accentPink : AppTheme.onSurfaceVariant,
                      onPressed: () {
                        ref.read(favoritesProvider.notifier).toggleFavorite(id);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            backgroundColor: AppTheme.backgroundMid,
                            duration: const Duration(seconds: 1),
                            content: Text(
                              isFav ? 'Removed from saved favorites' : 'Saved to favorites!',
                              style: AppTheme.bodyMd.copyWith(
                                color: isFav ? AppTheme.onSurface : AppTheme.accentPink,
                              ),
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
                          height: 240,
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
                                  const TintedIconBubble(
                                    icon: Icons.photo_library_rounded,
                                    tint: AppTheme.primaryLavender,
                                    size: 58,
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
                                  text: condition.toUpperCase(),
                                  tone: isNew ? NebulaBadgeTone.mint : NebulaBadgeTone.pink,
                                ),
                              ),
                              const Positioned(
                                bottom: 16,
                                right: 16,
                                child: NebulaBadgePill(
                                  text: 'ID VERIFIED',
                                  tone: NebulaBadgeTone.mint,
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
                                  fontSize: 30,
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
                              const NebulaBadgePill(
                                text: 'VERIFIED',
                                tone: NebulaBadgeTone.cyan,
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Next-Gen Feature 1: AI Price Intelligence & Deal Estimation
                        NextGenMarketplaceWidgets.buildAiValuationCard(
                          priceMinor: priceMinor,
                          condition: condition,
                          title: title,
                        ),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Next-Gen Feature 2: Official Device / Vehicle Verification
                        NextGenMarketplaceWidgets.buildVerificationBadges(title: title),

                        const SizedBox(height: AppTheme.sectionGap),

                        // Next-Gen Feature 3: 30s Live Inspection Video
                        NextGenMarketplaceWidgets.buildLiveVideoPreview(context, title: title),

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
                      vertical: 14,
                    ),
                    decoration: BoxDecoration(
                      color: AppTheme.backgroundMid.withValues(alpha: 0.88),
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
                              label: 'Call',
                              icon: Icons.phone_rounded,
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 14),
                              onPressed: () => _callSeller(sellerPhone),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            flex: 2,
                            child: NebulaButtonSecondary(
                              label: 'Safe Pay',
                              icon: Icons.shield_rounded,
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 14),
                              onPressed: () => NextGenMarketplaceWidgets.showEscrowSheet(
                                context,
                                title: title,
                                formattedPrice: formattedPrice,
                                priceMinor: priceMinor,
                              ),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            flex: 3,
                            child: NebulaButtonPrimary(
                              label: 'Direct Chat',
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 14),
                              icon: Icons.chat_bubble_rounded,
                              onPressed: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => ChatDetailScreen(
                                      conversationId: id,
                                      sellerName: sellerName,
                                      listingTitle: title,
                                      initialPrice: formattedPrice,
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
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
