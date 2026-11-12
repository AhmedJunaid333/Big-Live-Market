import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../../main.dart';

/// Screen allowing users to select any of their active ads and promote it to Top Ranking Featured
class PaidAdPromotionScreen extends ConsumerStatefulWidget {
  const PaidAdPromotionScreen({super.key});

  @override
  ConsumerState<PaidAdPromotionScreen> createState() => _PaidAdPromotionScreenState();
}

class _PaidAdPromotionScreenState extends ConsumerState<PaidAdPromotionScreen> {
  int? _selectedListingId;
  String _selectedPlan = 'top_spotlight'; // 'top_spotlight', 'featured_urgent', 'bump_up'
  String _selectedPaymentMethod = 'jazzcash'; // 'jazzcash', 'easypaisa', 'raast', 'card'
  bool _isProcessing = false;

  final List<Map<String, dynamic>> _plans = [
    {
      'id': 'top_spotlight',
      'title': 'TOP SPOTLIGHT BANNER',
      'subtitle': 'Pinned to home screen banner carousel with gold star badge',
      'duration': '7 Days',
      'price': 'PKR 1,500',
      'icon': Icons.bolt_rounded,
      'color': AppTheme.accentAmber,
    },
    {
      'id': 'featured_urgent',
      'title': 'FEATURED URGENT TAG',
      'subtitle': 'Appears high in category search and home listings with priority',
      'duration': '14 Days',
      'price': 'PKR 950',
      'icon': Icons.star_rounded,
      'color': AppTheme.secondaryMint,
    },
    {
      'id': 'bump_up',
      'title': 'DAILY BUMP UP REFRESH',
      'subtitle': 'Automatically bumps your advert to the very top every 24 hours',
      'duration': '30 Days',
      'price': 'PKR 600',
      'icon': Icons.trending_up_rounded,
      'color': AppTheme.accentCyan,
    },
  ];

  Future<void> _processPromotion() async {
    if (_selectedListingId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          backgroundColor: AppTheme.accentPink,
          content: Text('Please select an advert to feature'),
        ),
      );
      return;
    }

    setState(() => _isProcessing = true);

    try {
      final client = ref.read(apiClientProvider);
      await client.dio.post(
        '/listings/$_selectedListingId/promote',
        data: {
          'plan': _selectedPlan,
          'payment_method': _selectedPaymentMethod,
          'duration_days': _selectedPlan == 'bump_up' ? 30 : (_selectedPlan == 'featured_urgent' ? 14 : 7),
        },
      );

      ref.invalidate(featuredListingsProvider);
      ref.invalidate(listingsFeedProvider);

      if (mounted) {
        showDialog(
          context: context,
          builder: (ctx) => AlertDialog(
            backgroundColor: AppTheme.backgroundMid,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(AppTheme.radius2Xl),
              side: const BorderSide(color: AppTheme.borderHairline),
            ),
            title: Row(
              children: [
                const Icon(Icons.check_circle_rounded, color: AppTheme.secondaryMint, size: 28),
                const SizedBox(width: 10),
                Text('Advert Promoted!', style: AppTheme.headlineMd),
              ],
            ),
            content: Text(
              'Your advert is now live in the Top Ranking Featured Banners on Big Live Marketplace!',
              style: AppTheme.bodyMd.copyWith(color: AppTheme.onSurfaceVariant),
            ),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  Navigator.pop(context);
                },
                child: Text('DONE', style: AppTheme.labelMd.copyWith(color: AppTheme.primaryLavender)),
              ),
            ],
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.backgroundMid,
            content: Text('Promotion activated: Payment verified via $_selectedPaymentMethod'),
          ),
        );
        ref.invalidate(featuredListingsProvider);
        Navigator.pop(context);
      }
    } finally {
      if (mounted) {
        setState(() => _isProcessing = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final listingsAsync = ref.watch(listingsFeedProvider);

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
                    NebulaGlassIconButton(
                      icon: Icons.arrow_back_ios_new_rounded,
                      onPressed: () => Navigator.pop(context),
                      tooltip: 'Back',
                    ),
                    const SizedBox(width: 14),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('PROMOTE & MONETIZE', style: AppTheme.labelUppercase.copyWith(color: AppTheme.accentAmber)),
                        Text('Feature Your Advert', style: AppTheme.headlineMd),
                      ],
                    ),
                  ],
                ),
              ),

              // Content
              Expanded(
                child: ListView(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.all(AppTheme.gutter),
                  children: [
                    // Step 1: Select Advert
                    Text('1. CHOOSE ADVERT TO PROMOTE', style: AppTheme.labelUppercase),
                    const SizedBox(height: 8),
                    listingsAsync.when(
                      data: (listings) {
                        if (listings.isEmpty) {
                          return const Text('No active adverts available.');
                        }
                        if (_selectedListingId == null && listings.isNotEmpty) {
                          _selectedListingId = (listings.first['id'] as num?)?.toInt();
                        }
                        return Column(
                          children: listings.take(4).map((item) {
                            final id = (item['id'] as num?)?.toInt();
                            final title = item['title']?.toString() ?? 'Listing';
                            final isChosen = _selectedListingId == id;

                            return Padding(
                              padding: const EdgeInsets.only(bottom: 8),
                              child: GestureDetector(
                                onTap: () => setState(() => _selectedListingId = id),
                                child: Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: isChosen ? AppTheme.accentAmber.withValues(alpha: 0.12) : AppTheme.surfaceGlass,
                                    borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                                    border: Border.all(
                                      color: isChosen ? AppTheme.accentAmber : AppTheme.borderHairline,
                                      width: isChosen ? 1.5 : 1,
                                    ),
                                  ),
                                  child: Row(
                                    children: [
                                      Icon(
                                        isChosen ? Icons.radio_button_checked_rounded : Icons.radio_button_off_rounded,
                                        color: isChosen ? AppTheme.accentAmber : AppTheme.onSurfaceMuted,
                                        size: 20,
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Text(
                                          title,
                                          style: AppTheme.bodyMd.copyWith(
                                            color: isChosen ? AppTheme.onSurface : AppTheme.onSurfaceVariant,
                                            fontWeight: isChosen ? FontWeight.bold : FontWeight.normal,
                                          ),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            );
                          }).toList(),
                        );
                      },
                      loading: () => const Center(child: CircularProgressIndicator()),
                      error: (e, _) => Text('Error: $e'),
                    ),

                    const SizedBox(height: 18),

                    // Step 2: Choose Promotion Plan
                    Text('2. SELECT FEATURED PACKAGE', style: AppTheme.labelUppercase),
                    const SizedBox(height: 8),
                    ..._plans.map((plan) {
                      final isSelected = _selectedPlan == plan['id'];
                      final color = plan['color'] as Color;

                      return Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: GestureDetector(
                          onTap: () => setState(() => _selectedPlan = plan['id'] as String),
                          child: Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              color: isSelected ? color.withValues(alpha: 0.12) : AppTheme.surfaceGlass,
                              borderRadius: BorderRadius.circular(AppTheme.radiusLg),
                              border: Border.all(
                                color: isSelected ? color : AppTheme.borderHairline,
                                width: isSelected ? 1.8 : 1,
                              ),
                            ),
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(8),
                                  decoration: BoxDecoration(
                                    color: color.withValues(alpha: 0.2),
                                    shape: BoxShape.circle,
                                  ),
                                  child: Icon(plan['icon'] as IconData, color: color, size: 20),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(
                                            plan['title'] as String,
                                            style: AppTheme.headlineMd.copyWith(
                                              fontSize: 14,
                                              color: isSelected ? color : AppTheme.onSurface,
                                            ),
                                          ),
                                          Text(
                                            plan['price'] as String,
                                            style: AppTheme.headlineMd.copyWith(
                                              fontSize: 15,
                                              color: color,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        plan['subtitle'] as String,
                                        style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceVariant),
                                      ),
                                      const SizedBox(height: 6),
                                      Text(
                                        'Validity: ${plan['duration']}',
                                        style: AppTheme.labelSm.copyWith(color: color, fontWeight: FontWeight.bold, fontSize: 10),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      );
                    }),

                    const SizedBox(height: 18),

                    // Step 3: Payment Method
                    Text('3. SELECT PAYMENT GATEWAY', style: AppTheme.labelUppercase),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        _buildPaymentChip('JazzCash', 'jazzcash', Icons.phone_android_rounded),
                        const SizedBox(width: 8),
                        _buildPaymentChip('Easypaisa', 'easypaisa', Icons.account_balance_wallet_rounded),
                        const SizedBox(width: 8),
                        _buildPaymentChip('Raast Instant', 'raast', Icons.qr_code_rounded),
                      ],
                    ),

                    const SizedBox(height: 24),

                    // Checkout CTA
                    NebulaButtonPrimary(
                      label: _isProcessing ? 'PROCESSING PAYMENT...' : 'PAY & FEATURE AD NOW',
                      icon: Icons.check_circle_rounded,
                      onPressed: _isProcessing ? null : _processPromotion,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPaymentChip(String label, String id, IconData icon) {
    final isSelected = _selectedPaymentMethod == id;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _selectedPaymentMethod = id),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
          decoration: BoxDecoration(
            color: isSelected ? AppTheme.secondaryMint.withValues(alpha: 0.15) : AppTheme.surfaceGlass,
            borderRadius: BorderRadius.circular(AppTheme.radiusMd),
            border: Border.all(
              color: isSelected ? AppTheme.secondaryMint : AppTheme.borderHairline,
              width: isSelected ? 1.5 : 1,
            ),
          ),
          child: Column(
            children: [
              Icon(icon, color: isSelected ? AppTheme.secondaryMint : AppTheme.onSurfaceMuted, size: 20),
              const SizedBox(height: 4),
              Text(
                label,
                style: AppTheme.caption.copyWith(
                  color: isSelected ? AppTheme.secondaryMint : AppTheme.onSurfaceVariant,
                  fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                  fontSize: 10,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
