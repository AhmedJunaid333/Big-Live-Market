import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import '../providers/favorites_provider.dart';
import 'listing_detail_screen.dart';

class FavoritesScreen extends ConsumerWidget {
  const FavoritesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final favorites = ref.watch(favoritesProvider);
    final listingsAsync = ref.watch(listingsFeedProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Top Bar
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 12),
                child: Row(
                  children: [
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_ios_new_rounded,
                      onPressed: () => Navigator.pop(context),
                      tooltip: 'Back',
                    ),
                    const SizedBox(width: 14),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('SAVED ITEMS', style: AppTheme.labelUppercase),
                        Text(
                          AppStrings.favorites,
                          style: AppTheme.headlineMd.copyWith(color: AppTheme.onSurface),
                        ),
                      ],
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppTheme.accentCyan.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                        border: Border.all(color: AppTheme.accentCyan.withValues(alpha: 0.4)),
                      ),
                      child: Text(
                        '${favorites.length} Saved',
                        style: AppTheme.labelSm.copyWith(
                          color: AppTheme.accentCyan,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Content List
              Expanded(
                child: favorites.isEmpty
                    ? Center(
                        child: Padding(
                          padding: const EdgeInsets.all(AppTheme.gutter),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const TintedIconBubble(
                                icon: Icons.favorite_border_rounded,
                                tint: AppTheme.primaryLavender,
                                size: 64,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                AppStrings.noFavorites,
                                style: AppTheme.headlineMd,
                                textAlign: TextAlign.center,
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'Tap the heart icon on any ad to bookmark it here for fast access later.',
                                style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        ),
                      )
                    : listingsAsync.when(
                        data: (allListings) {
                          final savedListings = allListings.where((item) {
                            final id = (item is Map) ? (item['id'] as num?)?.toInt() : null;
                            return id != null && favorites.contains(id);
                          }).toList();

                          if (savedListings.isEmpty) {
                            // If listings not yet loaded or cached, show ids placeholder
                            return ListView.separated(
                              physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                              cacheExtent: 600,
                              padding: const EdgeInsets.all(AppTheme.gutter),
                              itemCount: favorites.length,
                              separatorBuilder: (_, __) => const SizedBox(height: 12),
                              itemBuilder: (ctx, index) {
                                final id = favorites.elementAt(index);
                                return RepaintBoundary(
                                  child: NebulaGlassCard(
                                  onTap: () {
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute(
                                        builder: (_) => ListingDetailScreen(listingId: id),
                                      ),
                                    );
                                  },
                                  child: Row(
                                    children: [
                                      const TintedIconBubble(
                                        icon: Icons.bookmark_added_rounded,
                                        tint: AppTheme.secondaryMint,
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text('Listing #$id', style: AppTheme.headlineMd),
                                            const SizedBox(height: 4),
                                            Text(
                                              'Tap to inspect advert details',
                                              style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
                                            ),
                                          ],
                                        ),
                                      ),
                                      IconButton(
                                        icon: const Icon(Icons.favorite_rounded, color: AppTheme.accentPink),
                                        onPressed: () => ref.read(favoritesProvider.notifier).toggleFavorite(id),
                                      ),
                                    ],
                                  ),
                                ),
                              );
                              },
                            );
                          }

                          return ListView.separated(
                            physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                            cacheExtent: 600,
                            padding: const EdgeInsets.all(AppTheme.gutter),
                            itemCount: savedListings.length,
                            separatorBuilder: (_, __) => const SizedBox(height: 12),
                            itemBuilder: (ctx, index) {
                              final item = savedListings[index] as Map<String, dynamic>;
                              final id = (item['id'] as num).toInt();
                              final title = item['title']?.toString() ?? 'Listing #$id';
                              final price = item['price_formatted']?.toString() ??
                                  'PKR ${(item['price_minor'] ?? 0) ~/ 100}';
                              final location = item['location_label']?.toString() ?? 'Pakistan';
                              final condition = item['condition_code']?.toString() ?? 'used';

                              return RepaintBoundary(
                                child: NebulaGlassCard(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (_) => ListingDetailScreen(listingId: id),
                                    ),
                                  );
                                },
                                child: Row(
                                  children: [
                                    Container(
                                      width: 68,
                                      height: 68,
                                      decoration: BoxDecoration(
                                        borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                        gradient: AppTheme.cardGradient,
                                        border: Border.all(color: AppTheme.borderHairline),
                                      ),
                                      child: const Icon(
                                        Icons.image_outlined,
                                        color: AppTheme.primaryLavender,
                                        size: 28,
                                      ),
                                    ),
                                    const SizedBox(width: 14),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            children: [
                                              NebulaBadgePill(
                                                text: condition.toUpperCase(),
                                                tone: NebulaBadgeTone.cyan,
                                              ),
                                              const SizedBox(width: 6),
                                              Expanded(
                                                child: Text(
                                                  location,
                                                  style: AppTheme.bodySm.copyWith(
                                                    color: AppTheme.onSurfaceVariant,
                                                    fontSize: 11,
                                                  ),
                                                  overflow: TextOverflow.ellipsis,
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            title,
                                            style: AppTheme.headlineMd,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                          const SizedBox(height: 2),
                                          Text(
                                            price,
                                            style: AppTheme.priceSm,
                                          ),
                                        ],
                                      ),
                                    ),
                                    IconButton(
                                      icon: const Icon(Icons.favorite_rounded, color: AppTheme.accentPink),
                                      onPressed: () => ref.read(favoritesProvider.notifier).toggleFavorite(id),
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
                          child: Text('Error loading favorites: $err', style: AppTheme.bodyMd),
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
