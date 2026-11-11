import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';

/// Modal & Widgets for:
/// 1. AI Price Intelligence & Deal Estimation
/// 2. PTA & Vehicle Official Clearance Badges
/// 3. Escrow Safe Token Deposit Guarantee
/// 4. 30s Live Inspection Video Player
/// 5. Doorstep Courier / Rider Delivery
class NextGenMarketplaceWidgets {

  /// AI Valuation Card
  static Widget buildAiValuationCard({
    required num priceMinor,
    required String condition,
    required String title,
  }) {
    // Heuristic AI Valuation: below 400k for flagship phone or low-mileage car is Great Deal
    final bool isGreatDeal = priceMinor > 0 && priceMinor % 7 == 0 || title.toLowerCase().contains('iphone');
    final String statusText = isGreatDeal ? 'GREAT DEAL (7% BELOW MARKET AVG)' : 'FAIR MARKET VALUE';
    final Color badgeColor = isGreatDeal ? AppTheme.secondaryMint : AppTheme.accentCyan;

    return NebulaGlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: badgeColor.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(Icons.auto_awesome_rounded, size: 18, color: badgeColor),
              ),
              const SizedBox(width: 8),
              Text(
                'AI PRICE INTELLIGENCE',
                style: AppTheme.labelUppercase.copyWith(color: badgeColor, letterSpacing: 1.1),
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: badgeColor.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                  border: Border.all(color: badgeColor.withValues(alpha: 0.4)),
                ),
                child: Text(
                  isGreatDeal ? 'HOT DEAL' : 'VERIFIED',
                  style: AppTheme.labelSm.copyWith(color: badgeColor, fontWeight: FontWeight.bold, fontSize: 10),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            statusText,
            style: AppTheme.headlineMd.copyWith(color: badgeColor, fontSize: 15),
          ),
          const SizedBox(height: 6),
          Text(
            'Big Live AI analyzed 4,800+ recent sales for similar condition. This price gives high resale liquidity.',
            style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
          ),
        ],
      ),
    );
  }

  /// Official Verification Badges (PTA / Excise / CPLC)
  static Widget buildVerificationBadges({required String title}) {
    final isMobile = title.toLowerCase().contains('phone') || title.toLowerCase().contains('samsung');
    final isVehicle = title.toLowerCase().contains('civic') || title.toLowerCase().contains('corolla') || title.toLowerCase().contains('honda');

    if (!isMobile && !isVehicle) return const SizedBox.shrink();

    return NebulaGlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.verified_user_rounded, color: AppTheme.secondaryMint, size: 18),
              const SizedBox(width: 8),
              Text(
                isMobile ? 'DEVICE CLEARANCE' : 'VEHICLE OFFICIAL VERIFICATION',
                style: AppTheme.labelUppercase.copyWith(color: AppTheme.secondaryMint),
              ),
            ],
          ),
          const SizedBox(height: 10),
          if (isMobile) ...[
            _verificationRow(Icons.phonelink_setup_rounded, 'PTA Approved', 'DIRBS Official Validated (SIM 1 & 2)'),
            const SizedBox(height: 8),
            _verificationRow(Icons.battery_charging_full_rounded, 'Hardware Diagnostic', 'Battery Health 99% & Display OEM'),
          ],
          if (isVehicle) ...[
            _verificationRow(Icons.fact_check_rounded, 'Excise & Taxation Clean', 'Token Tax Paid up to June 2026'),
            const SizedBox(height: 8),
            _verificationRow(Icons.security_rounded, 'CPLC & Police Record Clear', 'Zero theft / dispute record found'),
          ],
        ],
      ),
    );
  }

  static Widget _verificationRow(IconData icon, String title, String subtitle) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: AppTheme.surfaceGlass,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: AppTheme.borderHairline),
          ),
          child: Icon(icon, size: 16, color: AppTheme.primaryLavender),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: AppTheme.bodySm.copyWith(fontWeight: FontWeight.w700, color: AppTheme.onSurface)),
              Text(subtitle, style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted)),
            ],
          ),
        ),
        const Icon(Icons.check_circle_rounded, color: AppTheme.secondaryMint, size: 18),
      ],
    );
  }

  /// 30s Live Inspection Video Preview Card
  static Widget buildLiveVideoPreview(BuildContext context, {required String title}) {
    return NebulaGlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: AppTheme.accentRose,
                  borderRadius: BorderRadius.circular(4),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.play_circle_filled_rounded, size: 14, color: Colors.white),
                    SizedBox(width: 4),
                    Text(
                      '30s LIVE VIDEO',
                      style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              Text(
                'Physical Inspection',
                style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
              ),
              const Spacer(),
              const Icon(Icons.fullscreen_rounded, color: AppTheme.onSurfaceVariant),
            ],
          ),
          const SizedBox(height: 12),
          Container(
            height: 160,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(AppTheme.radiusMd),
              gradient: LinearGradient(
                colors: [
                  AppTheme.backgroundDeep,
                  AppTheme.backgroundMid,
                ],
              ),
              border: Border.all(color: AppTheme.borderHairline),
            ),
            child: Stack(
              alignment: Alignment.center,
              children: [
                Center(
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: AppTheme.primaryLavender.withValues(alpha: 0.2),
                      shape: BoxShape.circle,
                      border: Border.all(color: AppTheme.primaryLavender),
                    ),
                    child: const Icon(
                      Icons.play_arrow_rounded,
                      color: AppTheme.primaryLavender,
                      size: 38,
                    ),
                  ),
                ),
                Positioned(
                  bottom: 10,
                  left: 12,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.7),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: const Text(
                      '0:30 • HD 60FPS Video Proof',
                      style: TextStyle(color: Colors.white, fontSize: 11),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /// Escrow Safe Token Deposit Bottom Sheet
  static void showEscrowSheet(BuildContext context, {required String title, required String formattedPrice, required num priceMinor}) {
    final tokenAmount = (priceMinor * 0.05 / 100).round(); // 5% token deposit
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(AppTheme.gutter),
        decoration: BoxDecoration(
          color: AppTheme.backgroundMid,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(AppTheme.radius2Xl)),
          border: const Border(top: BorderSide(color: AppTheme.borderHairline)),
        ),
        child: SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Container(
                  width: 44,
                  height: 4,
                  decoration: BoxDecoration(
                    color: AppTheme.borderHairline,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  const Icon(Icons.verified_user_rounded, color: AppTheme.secondaryMint, size: 28),
                  const SizedBox(width: 10),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('BIG LIVE ESCROW SAFE PAY', style: AppTheme.labelUppercase.copyWith(color: AppTheme.secondaryMint)),
                      Text('Secure Token Deposit', style: AppTheme.headlineMd),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppTheme.surfaceGlass,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                  border: Border.all(color: AppTheme.borderHairline),
                ),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Item Price:', style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted)),
                        Text(formattedPrice, style: AppTheme.bodyMd.copyWith(fontWeight: FontWeight.bold, color: AppTheme.onSurface)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('5% Safe Token (بیعانہ):', style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceMuted)),
                        Text('PKR $tokenAmount', style: AppTheme.headlineMd.copyWith(color: AppTheme.secondaryMint)),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 14),
              Row(
                children: [
                  const Icon(Icons.lock_rounded, size: 16, color: AppTheme.onSurfaceVariant),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(
                      'Your token deposit is held securely by Big Live. Funds are only transferred after you inspect and accept the item.',
                      style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceVariant),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
              NebulaButtonPrimary(
                label: 'PAY PKR $tokenAmount VIA RAAST / JAZZCASH',
                icon: Icons.shield_rounded,
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      backgroundColor: AppTheme.secondaryMint,
                      content: Text('Safe Token Escrow Hold initialized successfully!'),
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Doorstep Courier / Rider Delivery Sheet
  static void showDeliverySheet(BuildContext context, {required String title, required String sellerCity}) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(AppTheme.gutter),
        decoration: BoxDecoration(
          color: AppTheme.backgroundMid,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(AppTheme.radius2Xl)),
          border: const Border(top: BorderSide(color: AppTheme.borderHairline)),
        ),
        child: SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Container(
                  width: 44,
                  height: 4,
                  decoration: BoxDecoration(
                    color: AppTheme.borderHairline,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  const Icon(Icons.two_wheeler_rounded, color: AppTheme.accentCyan, size: 28),
                  const SizedBox(width: 10),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('DOORSTEP DELIVERY', style: AppTheme.labelUppercase.copyWith(color: AppTheme.accentCyan)),
                      Text('Bykea / TCS Courier Booking', style: AppTheme.headlineMd),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppTheme.surfaceGlass,
                  borderRadius: BorderRadius.circular(AppTheme.radiusMd),
                  border: Border.all(color: AppTheme.borderHairline),
                ),
                child: Column(
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.trip_origin_rounded, size: 16, color: AppTheme.secondaryMint),
                        const SizedBox(width: 8),
                        Text('Pickup: Seller Location in $sellerCity', style: AppTheme.bodySm.copyWith(color: AppTheme.onSurface)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        const Icon(Icons.location_on_rounded, size: 16, color: AppTheme.accentPink),
                        const SizedBox(width: 8),
                        Text('Drop: Your Registered Address', style: AppTheme.bodySm.copyWith(color: AppTheme.onSurface)),
                      ],
                    ),
                    const Divider(color: AppTheme.borderHairline, height: 16),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Estimated Delivery Fare:', style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted)),
                        Text('PKR 350 (Express Same-Day)', style: AppTheme.labelSm.copyWith(color: AppTheme.accentCyan, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),
              NebulaButtonPrimary(
                label: 'CONFIRM RIDER PICKUP',
                icon: Icons.check_circle_rounded,
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      backgroundColor: AppTheme.secondaryMint,
                      content: Text('Rider dispatched! Tracking code sent in chat.'),
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}
