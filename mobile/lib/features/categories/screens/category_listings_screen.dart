import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import '../../listings/screens/listing_detail_screen.dart';
import '../models/category_taxonomy.dart';

final categoryListingsProvider = FutureProvider.family<List<dynamic>, int>((ref, categoryId) async {
  final client = ref.watch(apiClientProvider);
  try {
    final response = await client.dio.get('/listings?category_id=$categoryId');
    final data = response.data['data'] as List<dynamic>?;
    return List<dynamic>.from(data ?? []);
  } catch (e) {
    return <dynamic>[];
  }
});

class CategoryListingsScreen extends ConsumerStatefulWidget {
  final int categoryId;
  final String categoryName;
  final IconData icon;
  final Color accentColor;
  final SubcategoryItem? selectedSubcategory;
  final String? initialSearchQuery;

  const CategoryListingsScreen({
    super.key,
    required this.categoryId,
    required this.categoryName,
    required this.icon,
    required this.accentColor,
    this.selectedSubcategory,
    this.initialSearchQuery,
  });

  @override
  ConsumerState<CategoryListingsScreen> createState() => _CategoryListingsScreenState();
}

class _CategoryListingsScreenState extends ConsumerState<CategoryListingsScreen> {
  final _searchController = TextEditingController();
  String _localFilter = '';
  SubcategoryItem? _activeSubcategory;

  @override
  void initState() {
    super.initState();
    _activeSubcategory = widget.selectedSubcategory;
    if (widget.initialSearchQuery != null && widget.initialSearchQuery!.isNotEmpty) {
      _searchController.text = widget.initialSearchQuery!;
      _localFilter = widget.initialSearchQuery!.trim().toLowerCase();
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(categoryListingsProvider(widget.categoryId));
    final subcategories = CategoryTaxonomy.getSubcategoriesFor(widget.categoryId);

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic App Bar
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 12, AppTheme.gutter, 8),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_rounded,
                      color: AppTheme.onSurface,
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    const SizedBox(width: 14),
                    TintedIconBubble(
                      icon: _activeSubcategory?.icon ?? widget.icon,
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
                            _activeSubcategory == null ? widget.categoryName.toUpperCase() : widget.categoryName.toUpperCase(),
                            style: AppTheme.labelUppercase.copyWith(
                              color: widget.accentColor,
                              letterSpacing: 1.1,
                            ),
                          ),
                          Text(
                            _activeSubcategory?.name ?? widget.categoryName,
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
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 4),
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
                      hintText: _activeSubcategory != null
                          ? 'Search in ${_activeSubcategory!.name}...'
                          : 'Search in ${widget.categoryName}...',
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

              // Horizontal Subcategory Chips Filter
              if (subcategories.isNotEmpty) ...[
                const SizedBox(height: 6),
                SizedBox(
                  height: 42,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter),
                    itemCount: subcategories.length + 1,
                    separatorBuilder: (_, __) => const SizedBox(width: 8),
                    itemBuilder: (context, index) {
                      if (index == 0) {
                        final isAll = _activeSubcategory == null;
                        return InkWell(
                          onTap: () {
                            setState(() => _activeSubcategory = null);
                          },
                          borderRadius: BorderRadius.circular(20),
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 180),
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: isAll
                                  ? widget.accentColor.withValues(alpha: 0.28)
                                  : AppTheme.surfaceGlass,
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(
                                color: isAll ? widget.accentColor : AppTheme.borderHairline,
                                width: isAll ? 1.4 : 1.0,
                              ),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(
                                  Icons.grid_view_rounded,
                                  size: 15,
                                  color: isAll ? widget.accentColor : AppTheme.onSurfaceMuted,
                                ),
                                const SizedBox(width: 6),
                                Text(
                                  'All in ${widget.categoryName}',
                                  style: AppTheme.bodySm.copyWith(
                                    fontWeight: isAll ? FontWeight.w700 : FontWeight.w500,
                                    color: isAll ? AppTheme.onSurface : AppTheme.onSurfaceMuted,
                                    fontSize: 12,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      }

                      final sub = subcategories[index - 1];
                      final isSelected = _activeSubcategory?.slug == sub.slug;

                      return InkWell(
                        onTap: () {
                          setState(() {
                            if (isSelected) {
                              _activeSubcategory = null;
                            } else {
                              _activeSubcategory = sub;
                            }
                          });
                        },
                        borderRadius: BorderRadius.circular(20),
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 180),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          decoration: BoxDecoration(
                            color: isSelected
                                ? widget.accentColor.withValues(alpha: 0.28)
                                : AppTheme.surfaceGlass,
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(
                              color: isSelected ? widget.accentColor : AppTheme.borderHairline,
                              width: isSelected ? 1.4 : 1.0,
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                sub.icon,
                                size: 15,
                                color: isSelected ? widget.accentColor : AppTheme.onSurfaceMuted,
                              ),
                              const SizedBox(width: 6),
                              Text(
                                sub.name,
                                style: AppTheme.bodySm.copyWith(
                                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                                  color: isSelected ? AppTheme.onSurface : AppTheme.onSurfaceMuted,
                                  fontSize: 12,
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 6),
              ],

              // Category Listings List
              Expanded(
                child: listingsAsync.when(
                  data: (rawListings) {
                    final listings = rawListings.where((item) {
                      // Subcategory filter
                      if (_activeSubcategory != null) {
                        final itemSub = (item['subcategory_slug'] ?? '').toString();
                        if (itemSub.isNotEmpty && itemSub != _activeSubcategory!.slug) {
                          return false;
                        }
                      }

                      // Text search filter
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
                                icon: _activeSubcategory?.icon ?? widget.icon,
                                accentColor: widget.accentColor,
                                size: 68,
                                iconSize: 34,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                _activeSubcategory != null
                                    ? 'No listings in ${_activeSubcategory!.name}'
                                    : 'No listings in ${widget.categoryName}',
                                textAlign: TextAlign.center,
                                style: AppTheme.headlineMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'Be the first member to publish an advertisement in this subcategory!',
                                textAlign: TextAlign.center,
                                style: AppTheme.bodySm.copyWith(
                                  color: AppTheme.onSurfaceMuted,
                                ),
                              ),
                              if (_activeSubcategory != null) ...[
                                const SizedBox(height: 16),
                                OutlinedButton.icon(
                                  onPressed: () {
                                    setState(() => _activeSubcategory = null);
                                  },
                                  icon: const Icon(Icons.clear_all_rounded, size: 18),
                                  label: const Text('Show All Listings in Department'),
                                  style: OutlinedButton.styleFrom(
                                    foregroundColor: widget.accentColor,
                                    side: BorderSide(color: widget.accentColor),
                                  ),
                                ),
                              ],
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
                        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                        cacheExtent: 600,
                        padding: const EdgeInsets.fromLTRB(
                          AppTheme.gutter,
                          8,
                          AppTheme.gutter,
                          32,
                        ),
                        itemCount: listings.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 14),
                        itemBuilder: (context, index) {
                          final item = listings[index] as Map<String, dynamic>;
                          final priceMinor = (item['price_minor'] ?? 0) as int;
                          final priceRs = (priceMinor / 100).toStringAsFixed(0);
                          final currency = item['currency'] ?? 'PKR';
                          final condition = (item['condition_code'] ?? 'used').toString().toUpperCase();

                          return RepaintBoundary(
                            child: NebulaGlassCard(
                              borderRadius: AppTheme.radiusXl,
                              padding: const EdgeInsets.all(14),
                              onTap: () {
                                Navigator.of(context).push(
                                  MaterialPageRoute(
                                    builder: (_) => ListingDetailScreen(
                                      listingId: (item['id'] ?? (index + 1)) as int,
                                    ),
                                  ),
                                );
                              },
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Product Photo Placeholder with Gradient
                                  Container(
                                    width: 100,
                                    height: 100,
                                    decoration: BoxDecoration(
                                      color: AppTheme.backgroundMid,
                                      borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                                      border: Border.all(color: AppTheme.borderHairline),
                                    ),
                                    child: Center(
                                      child: Icon(
                                        _activeSubcategory?.icon ?? widget.icon,
                                        size: 38,
                                        color: widget.accentColor.withValues(alpha: 0.65),
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 14),

                                  // Details
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              '$currency $priceRs',
                                              style: AppTheme.headlineMd.copyWith(
                                                color: AppTheme.secondaryMint,
                                                fontWeight: FontWeight.w800,
                                                fontSize: 17,
                                              ),
                                            ),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                              decoration: BoxDecoration(
                                                color: condition == 'NEW'
                                                    ? AppTheme.accentMint.withValues(alpha: 0.15)
                                                    : AppTheme.accentAmber.withValues(alpha: 0.15),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: Text(
                                                condition,
                                                style: AppTheme.caption.copyWith(
                                                  color: condition == 'NEW'
                                                      ? AppTheme.accentMint
                                                      : AppTheme.accentAmber,
                                                  fontWeight: FontWeight.w700,
                                                  fontSize: 10,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          item['title'] ?? 'Listing Item',
                                          style: AppTheme.bodyMd.copyWith(
                                            fontWeight: FontWeight.w600,
                                            color: AppTheme.onSurface,
                                            fontSize: 14,
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 6),
                                        Row(
                                          children: [
                                            const Icon(
                                              Icons.location_on_outlined,
                                              size: 13,
                                              color: AppTheme.onSurfaceMuted,
                                            ),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                item['location_label'] ?? 'Pakistan',
                                                style: AppTheme.caption.copyWith(
                                                  color: AppTheme.onSurfaceMuted,
                                                ),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
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
                    );
                  },
                  loading: () => const Center(
                    child: CircularProgressIndicator(color: AppTheme.primaryLavender),
                  ),
                  error: (err, _) => Center(
                    child: Padding(
                      padding: const EdgeInsets.all(AppTheme.gutter),
                      child: Text(
                        'Failed to load listings: $err',
                        style: AppTheme.bodySm.copyWith(color: AppTheme.accentRose),
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
