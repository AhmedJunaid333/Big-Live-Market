import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import '../../listings/screens/listing_detail_screen.dart';

final categoryListingsProvider = FutureProvider.family<List<dynamic>, int>((ref, categoryId) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/listings?category_id=$categoryId');
    final data = response.data['data'] as List<dynamic>?;
    return data ?? [];
  } catch (e) {
    // Offline sample listings fallback filtered by category if available
    return [
      if (categoryId == 1 || categoryId == 2)
        {
          'id': 1,
          'title': 'Honda Civic RS Turbo 2022 Low Mileage',
          'description': 'First owner, bumper to bumper genuine, maintained through authorized dealership.',
          'price_minor': 850000000,
          'currency': 'PKR',
          'condition_code': 'used',
          'location_label': 'DHA Phase 5, Lahore',
          'seller': {'name': 'Tariq Mehmood', 'phone_e164': '+923001234567'}
        },
      if (categoryId == 3 || categoryId == 4)
        {
          'id': 2,
          'title': 'Apple iPhone 15 Pro Max 256GB Natural Titanium',
          'description': 'PTA approved, battery health 99%, complete original box and cable included.',
          'price_minor': 43000000,
          'currency': 'PKR',
          'condition_code': 'used',
          'location_label': 'Blue Area, Islamabad',
          'seller': {'name': 'Apple Store PK', 'phone_e164': '+923337654321'}
        },
      if (categoryId == 5)
        {
          'id': 3,
          'title': '1 Kanal Luxury Designer House for Sale',
          'description': 'Spanish architecture, 5 master bedrooms, imported Italian fittings, lush lawn.',
          'price_minor': 9500000000,
          'currency': 'PKR',
          'condition_code': 'new',
          'location_label': 'Clifton Block 4, Karachi',
          'seller': {'name': 'Prime Estates', 'phone_e164': '+923219876543'}
        },
    ];
  }
});

class CategoryListingsScreen extends ConsumerStatefulWidget {
  final int categoryId;
  final String categoryName;
  final IconData icon;
  final Color accentColor;

  const CategoryListingsScreen({
    super.key,
    required this.categoryId,
    required this.categoryName,
    required this.icon,
    required this.accentColor,
  });

  @override
  ConsumerState<CategoryListingsScreen> createState() => _CategoryListingsScreenState();
}

class _CategoryListingsScreenState extends ConsumerState<CategoryListingsScreen> {
  final _searchController = TextEditingController();
  String _localFilter = '';

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(categoryListingsProvider(widget.categoryId));

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic App Bar
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 12, AppTheme.gutter, 12),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      color: AppTheme.onSurface,
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    const SizedBox(width: 14),
                    TintedIconBubble(
                      icon: widget.icon,
                      accentColor: widget.accentColor,
                      size: 44,
                      iconSize: 22,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'DEPARTMENT',
                            style: AppTheme.labelUppercase.copyWith(
                              color: widget.accentColor,
                            ),
                          ),
                          Text(
                            widget.categoryName,
                            style: AppTheme.headlineMd.copyWith(
                              fontWeight: FontWeight.w700,
                              color: AppTheme.onSurface,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                    NebulaGlassIconButton(
                      icon: Icons.refresh_rounded,
                      color: widget.accentColor,
                      onPressed: () {
                        ref.invalidate(categoryListingsProvider(widget.categoryId));
                      },
                    ),
                  ],
                ),
              ),

              // Search Bar within this Category
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 8),
                child: Container(
                  decoration: BoxDecoration(
                    color: AppTheme.surfaceGlass,
                    borderRadius: BorderRadius.circular(AppTheme.radiusXl),
                    border: Border.all(color: AppTheme.borderHairline),
                  ),
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: TextField(
                    controller: _searchController,
                    style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurface),
                    decoration: InputDecoration(
                      hintText: 'Search in ${widget.categoryName}...',
                      hintStyle: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted),
                      prefixIcon: Icon(
                        Icons.search_rounded,
                        color: widget.accentColor,
                        size: 20,
                      ),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear_rounded, color: AppTheme.onSurfaceVariant, size: 18),
                              onPressed: () {
                                _searchController.clear();
                                setState(() => _localFilter = '');
                              },
                            )
                          : null,
                      border: InputBorder.none,
                    ),
                    onChanged: (val) {
                      setState(() => _localFilter = val.trim().toLowerCase());
                    },
                  ),
                ),
              ),

              // Category Listings List
              Expanded(
                child: listingsAsync.when(
                  data: (rawListings) {
                    final listings = rawListings.where((item) {
                      if (_localFilter.isEmpty) return true;
                      final title = (item['title'] ?? '').toString().toLowerCase();
                      final desc = (item['description'] ?? '').toString().toLowerCase();
                      final loc = (item['location_label'] ?? '').toString().toLowerCase();
                      return title.contains(_localFilter) || desc.contains(_localFilter) || loc.contains(_localFilter);
                    }).toList();

                    if (listings.isEmpty) {
                      return Center(
                        child: Padding(
                          padding: const EdgeInsets.all(AppTheme.gutter),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              TintedIconBubble(
                                icon: widget.icon,
                                accentColor: widget.accentColor,
                                size: 68,
                                iconSize: 34,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'No listings in ${widget.categoryName}',
                                textAlign: TextAlign.center,
                                style: AppTheme.headlineMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'Be the first member to publish an advertisement in this department!',
                                textAlign: TextAlign.center,
                                style: AppTheme.bodySm.copyWith(
                                  color: AppTheme.onSurfaceMuted,
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    }

                    return RefreshIndicator(
                      color: widget.accentColor,
                      backgroundColor: AppTheme.backgroundMid,
                      onRefresh: () async {
                        ref.invalidate(categoryListingsProvider(widget.categoryId));
                      },
                      child: ListView.separated(
                        padding: const EdgeInsets.fromLTRB(
                          AppTheme.gutter,
                          12,
                          AppTheme.gutter,
                          32,
                        ),
                        itemCount: listings.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 14),
                        itemBuilder: (context, index) {
                          final item = listings[index] as Map<String, dynamic>;
                          final priceMinor = (item['price_minor'] ?? 0) as num;
                          final currency = item['currency']?.toString() ?? 'PKR';
                          final formattedPrice = '$currency ${(priceMinor / 100).toStringAsFixed(0)}';
                          final location = item['location_label']?.toString() ?? 'Pakistan';
                          final condition = item['condition_code']?.toString() ?? 'used';
                          final isNew = condition.toLowerCase() == 'new';

                          return NebulaGlassCard(
                            padding: const EdgeInsets.all(16),
                            borderRadius: AppTheme.radius2Xl,
                            onTap: () {
                              Navigator.of(context).push(
                                MaterialPageRoute(
                                  builder: (_) => ListingDetailScreen(listing: item),
                                ),
                              );
                            },
                            child: Row(
                              children: [
                                // Thumbnail
                                Container(
                                  width: 84,
                                  height: 84,
                                  decoration: BoxDecoration(
                                    color: AppTheme.backgroundMid,
                                    borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                    border: Border.all(color: AppTheme.borderHairline),
                                  ),
                                  child: Stack(
                                    alignment: Alignment.center,
                                    children: [
                                      Icon(
                                        widget.icon,
                                        color: widget.accentColor.withValues(alpha: 0.6),
                                        size: 32,
                                      ),
                                      Positioned(
                                        bottom: 4,
                                        left: 4,
                                        child: NebulaBadgePill(
                                          label: condition,
                                          color: isNew ? AppTheme.accentMint : AppTheme.accentAmber,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(width: 14),

                                // Info
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        item['title']?.toString() ?? 'Untitled',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: AppTheme.bodyMd.copyWith(
                                          fontWeight: FontWeight.w700,
                                          color: AppTheme.onSurface,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Row(
                                        children: [
                                          const Icon(
                                            Icons.location_on_rounded,
                                            size: 13,
                                            color: AppTheme.onSurfaceMuted,
                                          ),
                                          const SizedBox(width: 4),
                                          Expanded(
                                            child: Text(
                                              location,
                                              maxLines: 1,
                                              overflow: TextOverflow.ellipsis,
                                              style: AppTheme.bodySm.copyWith(
                                                color: AppTheme.onSurfaceVariant,
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 6),
                                      Text(
                                        formattedPrice,
                                        style: AppTheme.numeralLg.copyWith(
                                          color: widget.accentColor,
                                          shadows: [
                                            Shadow(
                                              color: widget.accentColor.withValues(alpha: 0.4),
                                              blurRadius: 8,
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const Icon(
                                  Icons.chevron_right_rounded,
                                  color: AppTheme.onSurfaceMuted,
                                  size: 22,
                                ),
                              ],
                            ),
                          );
                        },
                      ),
                    );
                  },
                  loading: () => Center(
                    child: CircularProgressIndicator(color: widget.accentColor),
                  ),
                  error: (err, _) => Center(
                    child: Text(
                      'Error loading listings: $err',
                      style: AppTheme.bodyMd.copyWith(color: AppTheme.error),
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
