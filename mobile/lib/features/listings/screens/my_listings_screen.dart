import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';
import 'listing_detail_screen.dart';
import 'post_ad_screen.dart';

final myListingsFutureProvider = FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.dio.get('/me/listings');
  final data = response.data['data'];
  if (data is List) {
    return data;
  }
  return [];
});

class MyListingsScreen extends ConsumerStatefulWidget {
  const MyListingsScreen({super.key});

  @override
  ConsumerState<MyListingsScreen> createState() => _MyListingsScreenState();
}

class _MyListingsScreenState extends ConsumerState<MyListingsScreen> {
  String _selectedTab = 'all'; // 'all', 'active', 'pending', 'sold'

  Future<void> _markSold(int id) async {
    try {
      final client = ref.read(apiClientProvider);
      await client.dio.post('/listings/$id/mark-sold');
      ref.invalidate(myListingsFutureProvider);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: AppTheme.secondaryMint,
            content: Text('Listing marked as sold!'),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.error,
            content: Text('Failed to mark sold: $e'),
          ),
        );
      }
    }
  }

  Future<void> _togglePause(int id, bool isPaused) async {
    try {
      final client = ref.read(apiClientProvider);
      final endpoint = isPaused ? '/listings/$id/resume' : '/listings/$id/pause';
      await client.dio.post(endpoint);
      ref.invalidate(myListingsFutureProvider);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.secondaryMint,
            content: Text(isPaused ? 'Listing resumed to active' : 'Listing paused'),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.error,
            content: Text('Action failed: $e'),
          ),
        );
      }
    }
  }

  Future<void> _deleteListing(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppTheme.backgroundMid,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppTheme.radiusXl),
          side: const BorderSide(color: AppTheme.borderHairline),
        ),
        title: Text('Delete Listing?', style: AppTheme.headlineMd),
        content: Text(
          'Are you sure you want to permanently remove this advert?',
          style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: Text('CANCEL', style: AppTheme.labelMd.copyWith(color: AppTheme.onSurfaceVariant)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: Text('DELETE', style: AppTheme.labelMd.copyWith(color: AppTheme.accentPink)),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    try {
      final client = ref.read(apiClientProvider);
      await client.dio.delete('/listings/$id');
      ref.invalidate(myListingsFutureProvider);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: AppTheme.secondaryMint,
            content: Text('Listing removed successfully'),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.error,
            content: Text('Failed to delete: $e'),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(myListingsFutureProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Header
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 12),
                child: Row(
                  children: [
                    if (Navigator.canPop(context)) ...[
                      NebulaGlassIconButton(
                        icon: Icons.arrow_back_ios_new_rounded,
                        onPressed: () => Navigator.pop(context),
                        tooltip: 'Back',
                      ),
                      const SizedBox(width: 14),
                    ] else ...[
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: AppTheme.surfaceGlass,
                          borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                          border: Border.all(color: AppTheme.borderHairline),
                        ),
                        child: const Icon(
                          Icons.view_agenda_rounded,
                          color: Color(0xFF38BDF8),
                          size: 20,
                        ),
                      ),
                      const SizedBox(width: 12),
                    ],
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('SELLER WORKSPACE', style: AppTheme.labelUppercase),
                        Text('My Ads', style: AppTheme.headlineMd),
                      ],
                    ),
                    const Spacer(),
                    NebulaGlassIconButton(
                      icon: Icons.add_rounded,
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const PostAdScreen()),
                        ).then((_) => ref.invalidate(myListingsFutureProvider));
                      },
                      tooltip: 'Create Ad',
                    ),
                  ],
                ),
              ),

              // Filter Tabs
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppTheme.gutter, vertical: 6),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _buildTabChip('All', 'all'),
                      const SizedBox(width: 8),
                      _buildTabChip('Active', 'active'),
                      const SizedBox(width: 8),
                      _buildTabChip('Pending / Draft', 'pending'),
                      const SizedBox(width: 8),
                      _buildTabChip('Sold', 'sold'),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 8),

              // Content List
              Expanded(
                child: listingsAsync.when(
                  data: (listings) {
                    final filtered = listings.where((item) {
                      if (item is! Map) return false;
                      final status = item['status']?.toString() ?? 'draft';
                      if (_selectedTab == 'all') return true;
                      if (_selectedTab == 'active') return status == 'active';
                      if (_selectedTab == 'sold') return status == 'sold';
                      if (_selectedTab == 'pending') {
                        return status == 'draft' || status == 'pending_review' || status == 'paused';
                      }
                      return true;
                    }).toList();

                    if (filtered.isEmpty) {
                      return Center(
                        child: Padding(
                          padding: const EdgeInsets.all(AppTheme.gutter),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const TintedIconBubble(
                                icon: Icons.storefront_outlined,
                                tint: AppTheme.primaryLavender,
                                size: 56,
                              ),
                              const SizedBox(height: 16),
                              Text('No listings in this view', style: AppTheme.headlineMd),
                              const SizedBox(height: 8),
                              Text(
                                'Post your vehicles, gadgets or property to reach buyers instantly.',
                                style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
                                textAlign: TextAlign.center,
                              ),
                              const SizedBox(height: 20),
                              NebulaButtonPrimary(
                                label: 'POST AN ADVERT',
                                icon: Icons.add_rounded,
                                onPressed: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (_) => const PostAdScreen()),
                                  ).then((_) => ref.invalidate(myListingsFutureProvider));
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    }

                    return RefreshIndicator(
                      color: AppTheme.primaryLavender,
                      onRefresh: () async => ref.invalidate(myListingsFutureProvider),
                      child: ListView.separated(
                        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                        cacheExtent: 600,
                        padding: const EdgeInsets.all(AppTheme.gutter),
                        itemCount: filtered.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 12),
                        itemBuilder: (ctx, index) {
                          final item = filtered[index] as Map<String, dynamic>;
                          final id = (item['id'] as num).toInt();
                          final title = item['title']?.toString() ?? 'Listing #$id';
                          final status = item['status']?.toString() ?? 'draft';
                          final priceFormatted = item['price_formatted']?.toString() ??
                              'PKR ${(item['price_minor'] ?? 0) ~/ 100}';
                          final isPaused = status == 'paused';
                          final isSold = status == 'sold';

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
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      width: 60,
                                      height: 60,
                                      decoration: BoxDecoration(
                                        borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                        gradient: AppTheme.cardGradient,
                                        border: Border.all(color: AppTheme.borderHairline),
                                      ),
                                      child: const Icon(
                                        Icons.inventory_2_outlined,
                                        color: AppTheme.primaryLavender,
                                        size: 26,
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            children: [
                                              _buildStatusBadge(status),
                                              const Spacer(),
                                              Text(
                                                priceFormatted,
                                                style: AppTheme.priceSm,
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 6),
                                          Text(
                                            title,
                                            style: AppTheme.headlineMd,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 12),
                                const Divider(color: AppTheme.borderHairline, height: 1),
                                const SizedBox(height: 10),

                                // Action Buttons
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.end,
                                  children: [
                                    if (!isSold) ...[
                                      TextButton.icon(
                                        onPressed: () => _togglePause(id, isPaused),
                                        icon: Icon(
                                          isPaused ? Icons.play_arrow_rounded : Icons.pause_rounded,
                                          size: 16,
                                          color: AppTheme.accentCyan,
                                        ),
                                        label: Text(
                                          isPaused ? 'Resume' : 'Pause',
                                          style: AppTheme.labelSm.copyWith(color: AppTheme.accentCyan),
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      TextButton.icon(
                                        onPressed: () => _markSold(id),
                                        icon: const Icon(
                                          Icons.check_circle_outline_rounded,
                                          size: 16,
                                          color: AppTheme.secondaryMint,
                                        ),
                                        label: Text(
                                          'Mark Sold',
                                          style: AppTheme.labelSm.copyWith(color: AppTheme.secondaryMint),
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                    ],
                                    IconButton(
                                      icon: const Icon(Icons.delete_outline_rounded, color: AppTheme.accentPink, size: 20),
                                      onPressed: () => _deleteListing(id),
                                      tooltip: 'Delete',
                                    ),
                                  ],
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
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            'Sign in as a Seller to manage your ads',
                            style: AppTheme.headlineMd,
                            textAlign: TextAlign.center,
                          ),
                          const SizedBox(height: 12),
                          Text(
                            '$err',
                            style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
                            textAlign: TextAlign.center,
                          ),
                        ],
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

  Widget _buildTabChip(String label, String value) {
    final isSelected = _selectedTab == value;
    return GestureDetector(
      onTap: () => setState(() => _selectedTab = value),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? AppTheme.primaryLavender.withValues(alpha: 0.25) : AppTheme.glassSurface,
          borderRadius: BorderRadius.circular(AppTheme.radiusFull),
          border: Border.all(
            color: isSelected ? AppTheme.primaryLavender : AppTheme.borderHairline,
            width: isSelected ? 1.5 : 1,
          ),
        ),
        child: Text(
          label,
          style: AppTheme.labelSm.copyWith(
            color: isSelected ? AppTheme.primaryLavender : AppTheme.onSurfaceVariant,
            fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
          ),
        ),
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    switch (status) {
      case 'active':
        return const NebulaBadgePill(text: 'ACTIVE', tone: NebulaBadgeTone.mint);
      case 'sold':
        return const NebulaBadgePill(text: 'SOLD', tone: NebulaBadgeTone.cyan);
      case 'paused':
        return const NebulaBadgePill(text: 'PAUSED', tone: NebulaBadgeTone.pink);
      default:
        return NebulaBadgePill(text: status.toUpperCase(), tone: NebulaBadgeTone.lavender);
    }
  }
}
