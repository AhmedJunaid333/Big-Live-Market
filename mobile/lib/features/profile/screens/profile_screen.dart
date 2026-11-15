import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/localization/app_strings.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/nebula_glass_widgets.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/screens/login_screen.dart';
import '../../earn/providers/earn_provider.dart';
import '../../earn/widgets/withdrawal_bottom_sheet.dart';
import '../../listings/providers/favorites_provider.dart';
import '../../listings/screens/favorites_screen.dart';
import '../../listings/screens/my_listings_screen.dart';
import 'devices_screen.dart';
import 'edit_profile_screen.dart';
import 'paid_ad_promotion_screen.dart';
import 'verification_screen.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  void _openWithdrawalSheet(int availableMinor) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => WithdrawalBottomSheet(
        availableMinor: availableMinor,
        onWithdraw: (amountMinor, method, beneficiary, idempotencyKey) async {
          await ref.read(earnProvider.notifier).requestWithdrawal(
                amountMinor: amountMinor,
                method: method,
                beneficiary: beneficiary,
                idempotencyKey: idempotencyKey,
              );
        },
      ),
    );
  }

  void _openHistorySheet(EarnState earnState) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => Container(
        decoration: const BoxDecoration(
          color: Color(0xFF0B0F19),
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Center(
              child: Container(
                width: 44,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.white24,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: const Color(0xFF38BDF8).withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.receipt_long_rounded, color: Color(0xFF38BDF8), size: 22),
                ),
                const SizedBox(width: 10),
                const Text(
                  'Earning & Payout Passbook (ریکارڈ)',
                  style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.w800),
                ),
              ],
            ),
            const SizedBox(height: 16),
            if (earnState.withdrawals.isEmpty)
              Container(
                padding: const EdgeInsets.all(28),
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  color: const Color(0xFF131B2E),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Column(
                  children: [
                    Icon(Icons.history_rounded, color: Color(0xFF64748B), size: 36),
                    SizedBox(height: 8),
                    Text(
                      'No withdrawal requests submitted yet.',
                      style: TextStyle(color: Color(0xFF94A3B8), fontSize: 13),
                    ),
                  ],
                ),
              )
            else
              ...earnState.withdrawals.map((w) => Container(
                    margin: const EdgeInsets.only(bottom: 8),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: const Color(0xFF131B2E),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: const Color(0xFF334155)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'PKR ${w.amountPkr.toStringAsFixed(0)} • ${w.method.toUpperCase()}',
                              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              w.date,
                              style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 11),
                            ),
                          ],
                        ),
                        NebulaBadgePill(
                          text: w.status.toUpperCase(),
                          tone: w.status == 'paid' ? NebulaBadgeTone.mint : NebulaBadgeTone.lavender,
                        ),
                      ],
                    ),
                  )),
          ],
        ),
      ),
    );
  }

  void _showTrustScoreDialog(BuildContext context, dynamic user) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF0F172A),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(24),
          side: const BorderSide(color: Color(0xFF334155)),
        ),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: const Color(0xFFFBBF24).withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.verified_user_rounded, color: Color(0xFFFBBF24), size: 22),
            ),
            const SizedBox(width: 10),
            const Text(
              'Trust & Reputation',
              style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFF1E293B),
                borderRadius: BorderRadius.circular(14),
              ),
              child: const Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('Trust Index Score', style: TextStyle(color: Color(0xFF94A3B8), fontSize: 13)),
                  Text(
                    '98 / 100 (Level 1)',
                    style: TextStyle(color: Color(0xFF34D399), fontWeight: FontWeight.bold, fontSize: 14),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            _buildTrustRow(Icons.check_circle_rounded, const Color(0xFF34D399), 'Phone Verified (+92 Pakistan)'),
            const SizedBox(height: 8),
            _buildTrustRow(Icons.check_circle_rounded, const Color(0xFF34D399), 'Email Identity Bound'),
            const SizedBox(height: 8),
            _buildTrustRow(Icons.shield_rounded, const Color(0xFF38BDF8), 'Safe Pay Escrow Eligible'),
            const SizedBox(height: 8),
            _buildTrustRow(Icons.stars_rounded, const Color(0xFFFBBF24), 'Community Feedback: 100% Positive'),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('CLOSE', style: TextStyle(color: Color(0xFF38BDF8), fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  static Widget _buildTrustRow(IconData icon, Color color, String text) {
    return Row(
      children: [
        Icon(icon, color: color, size: 16),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            text,
            style: const TextStyle(color: Colors.white70, fontSize: 12),
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final lang = ref.watch(languageProvider);
    final earnState = ref.watch(earnProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: NebulaAtmosphericBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Cosmic Header
              Padding(
                padding: const EdgeInsets.fromLTRB(AppTheme.gutter, 16, AppTheme.gutter, 12),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'ACCOUNT & PREFERENCES',
                          style: AppTheme.labelUppercase,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          AppStrings.tabProfile,
                          style: AppTheme.displayLg,
                        ),
                      ],
                    ),
                    // Quick Language Switcher Pill
                    GestureDetector(
                      onTap: () => ref.read(languageProvider.notifier).toggleLanguage(),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryLavender.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                          border: Border.all(color: AppTheme.primaryLavender.withValues(alpha: 0.35)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.language_rounded, size: 14, color: AppTheme.primaryLavender),
                            const SizedBox(width: 6),
                            Text(
                              lang == AppLanguage.ur ? 'اردو' : 'EN',
                              style: AppTheme.labelSm.copyWith(
                                color: AppTheme.primaryLavender,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Profile Content Body
              Expanded(
                child: authState.isAuthenticated && authState.user != null
                    ? _buildAuthenticatedProfile(context, ref, authState, lang, earnState)
                    : _buildGuestProfile(context, ref, lang),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildGuestProfile(BuildContext context, WidgetRef ref, AppLanguage lang) {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(
          AppTheme.gutter,
          16,
          AppTheme.gutter,
          120, // Clear floating bottom tab bar
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            NebulaGlassCard(
              padding: const EdgeInsets.all(28),
              child: Column(
                children: [
                  const Icon(
                    Icons.account_circle_outlined,
                    size: 72,
                    color: AppTheme.primaryLavender,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Welcome to Big Live',
                    style: AppTheme.headlineMd.copyWith(
                      color: AppTheme.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Sign in to post advertisements, manage orders, view favorites, and access your earning wallet.',
                    textAlign: TextAlign.center,
                    style: AppTheme.bodySm.copyWith(color: AppTheme.onSurfaceVariant),
                  ),
                  const SizedBox(height: 24),
                  NebulaButtonPrimary(
                    label: AppStrings.signIn,
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const LoginScreen()),
                      );
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            NebulaGlassCard(
              padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
              child: Column(
                children: [
                  _buildMenuRow(
                    icon: Icons.favorite_rounded,
                    accentColor: AppTheme.accentPink,
                    title: AppStrings.favorites,
                    subtitle: 'View your bookmarked advertisements',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                      );
                    },
                  ),
                  const Divider(color: AppTheme.borderHairline),
                  _buildMenuRow(
                    icon: Icons.bolt_rounded,
                    accentColor: AppTheme.accentAmber,
                    title: 'Promote Advert (پیڈ ایڈ)',
                    subtitle: 'Pay & feature your listing in top banner',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const PaidAdPromotionScreen()),
                      );
                    },
                  ),
                  const Divider(color: AppTheme.borderHairline),
                  _buildMenuRow(
                    icon: Icons.language_rounded,
                    accentColor: AppTheme.accentCyan,
                    title: AppStrings.languageSetting,
                    subtitle: lang == AppLanguage.ur ? 'اردو فعال ہے (تبدیل کریں)' : 'English active (Switch to Urdu)',
                    onTap: () => ref.read(languageProvider.notifier).toggleLanguage(),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAuthenticatedProfile(
    BuildContext context,
    WidgetRef ref,
    AuthState authState,
    AppLanguage lang,
    EarnState earnState,
  ) {
    final user = authState.user!;

    return ListView(
      padding: const EdgeInsets.fromLTRB(
        AppTheme.gutter,
        8,
        AppTheme.gutter,
        120, // Clear floating bottom tab bar
      ),
      children: [
        // User Identity Card
        NebulaGlassCard(
          padding: const EdgeInsets.all(20),
          child: Column(
            children: [
              Row(
                children: [
                  NebulaAvatar(
                    size: 64,
                    initials: user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                    imagePath: user.avatarPath,
                    isOnline: true,
                    showEditBadge: true,
                    onEditTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const EditProfileScreen()),
                      );
                    },
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                user.name,
                                style: AppTheme.bodyMd.copyWith(
                                  fontWeight: FontWeight.w700,
                                  fontSize: 18,
                                  color: AppTheme.onSurface,
                                ),
                              ),
                            ),
                            // Quick Edit Profile Action Button
                            GestureDetector(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (_) => const EditProfileScreen()),
                                );
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: const Color(0xFF38BDF8).withValues(alpha: 0.15),
                                  borderRadius: BorderRadius.circular(AppTheme.radiusFull),
                                  border: Border.all(
                                    color: const Color(0xFF38BDF8).withValues(alpha: 0.35),
                                  ),
                                ),
                                child: const Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Icon(Icons.edit_rounded, size: 12, color: Color(0xFF38BDF8)),
                                    SizedBox(width: 4),
                                    Text(
                                      'Edit',
                                      style: TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                        color: Color(0xFF38BDF8),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 3),
                        Text(
                          user.email ?? user.phoneE164 ?? '',
                          style: AppTheme.bodySm.copyWith(
                            color: AppTheme.onSurfaceVariant,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 8,
                          runSpacing: 6,
                          crossAxisAlignment: WrapCrossAlignment.center,
                          children: [
                            NebulaBadgePill(
                              text: user.roles.isNotEmpty ? user.roles.first.toUpperCase() : 'BUYER',
                              tone: NebulaBadgeTone.mint,
                            ),
                            // Unique ID badge with 1-tap copy
                            GestureDetector(
                              onTap: () {
                                Clipboard.setData(ClipboardData(text: user.uniqueCode));
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('Copied User ID: ${user.uniqueCode}'),
                                    duration: const Duration(seconds: 2),
                                    behavior: SnackBarBehavior.floating,
                                    backgroundColor: const Color(0xFF1E293B),
                                  ),
                                );
                              },
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFFBBF24).withValues(alpha: 0.12),
                                  borderRadius: BorderRadius.circular(6),
                                  border: Border.all(
                                    color: const Color(0xFFFBBF24).withValues(alpha: 0.3),
                                  ),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Icon(Icons.fingerprint_rounded, size: 12, color: Color(0xFFFBBF24)),
                                    const SizedBox(width: 4),
                                    Text(
                                      user.uniqueCode,
                                      style: const TextStyle(
                                        fontFamily: 'monospace',
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                        color: Color(0xFFFBBF24),
                                      ),
                                    ),
                                    const SizedBox(width: 4),
                                    const Icon(Icons.copy_rounded, size: 11, color: Color(0xFFFBBF24)),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),

        const SizedBox(height: 14),

        // Sponsored Earning Wallet Card (Profile Adjustment for Earning)
        NebulaGlassCard(
          padding: const EdgeInsets.all(16),
          borderRadius: 20,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: const Color(0xFF34D399).withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(
                      Icons.account_balance_wallet_rounded,
                      color: Color(0xFF34D399),
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 10),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Earning Wallet (میری کمائی)',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        Text(
                          'Sponsored missions & task rewards',
                          style: TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  const NebulaBadgePill(
                    text: 'ACTIVE',
                    tone: NebulaBadgeTone.mint,
                  ),
                ],
              ),
              const SizedBox(height: 14),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFF0F172A).withValues(alpha: 0.6),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFF1E293B)),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Available Balance (دستیاب رقم)',
                          style: TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'PKR ${(earnState.availableMinor / 100.0).toStringAsFixed(0)}',
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF34D399),
                            letterSpacing: -0.5,
                          ),
                        ),
                      ],
                    ),
                    if (earnState.heldMinor > 0)
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text(
                            'In Review (زیر التواء)',
                            style: TextStyle(fontSize: 11, color: Color(0xFFFBBF24)),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'PKR ${(earnState.heldMinor / 100.0).toStringAsFixed(0)}',
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w700,
                              color: Color(0xFFFBBF24),
                            ),
                          ),
                        ],
                      ),
                  ],
                ),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () => _openWithdrawalSheet(earnState.availableMinor),
                      icon: const Icon(Icons.outbox_rounded, size: 16),
                      label: const Text(
                        'Withdraw',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF059669),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 11),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () => _openHistorySheet(earnState),
                      icon: const Icon(Icons.receipt_long_rounded, size: 16, color: Color(0xFF38BDF8)),
                      label: const Text(
                        'Passbook',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Color(0xFF38BDF8)),
                      ),
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(color: Color(0xFF38BDF8)),
                        padding: const EdgeInsets.symmetric(vertical: 11),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),

        const SizedBox(height: 14),

        // Quick Stats Tiles
        Builder(
          builder: (context) {
            final myListingsAsync = ref.watch(myListingsFutureProvider);
            final favorites = ref.watch(favoritesProvider);

            return Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const MyListingsScreen()),
                      );
                    },
                    child: NebulaGlassCard(
                      padding: const EdgeInsets.all(14),
                      child: Column(
                        children: [
                          myListingsAsync.when(
                            data: (listings) => Text(
                              '${listings.length}',
                              style: AppTheme.numeralLg.copyWith(
                                color: AppTheme.primaryLavender,
                              ),
                            ),
                            loading: () => const SizedBox(
                              height: 20,
                              width: 20,
                              child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primaryLavender),
                            ),
                            error: (_, __) => Text(
                              '0',
                              style: AppTheme.numeralLg.copyWith(
                                color: AppTheme.primaryLavender,
                              ),
                            ),
                          ),
                          const SizedBox(height: 4),
                          FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Text(
                              'Active Ads',
                              style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                              maxLines: 1,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: GestureDetector(
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                      );
                    },
                    child: NebulaGlassCard(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        children: [
                          Text(
                            '${favorites.length}',
                            style: AppTheme.numeralLg.copyWith(
                              color: AppTheme.accentPink,
                            ),
                          ),
                          const SizedBox(height: 4),
                          FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Text(
                              'Favorites',
                              style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                              maxLines: 1,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: GestureDetector(
                    onTap: () => _showTrustScoreDialog(context, user),
                    child: NebulaGlassCard(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        children: [
                          Text(
                            '98%',
                            style: AppTheme.numeralLg.copyWith(
                              color: AppTheme.accentAmber,
                            ),
                          ),
                          const SizedBox(height: 4),
                          FittedBox(
                            fit: BoxFit.scaleDown,
                            child: Text(
                              'Trust Score ★',
                              style: AppTheme.caption.copyWith(color: AppTheme.onSurfaceMuted),
                              maxLines: 1,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            );
          },
        ),

        const SizedBox(height: 16),

        // Menu Items List
        NebulaGlassCard(
          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
          child: Column(
            children: [
              _buildMenuRow(
                icon: Icons.manage_accounts_rounded,
                accentColor: const Color(0xFF38BDF8),
                title: 'Edit Profile',
                subtitle: 'Update your display name, phone, or photo',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const EditProfileScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.account_balance_wallet_rounded,
                accentColor: const Color(0xFF34D399),
                title: 'Earning Wallet & Payouts (کمائی اور کیش آؤٹ)',
                subtitle: 'JazzCash, Easypaisa, and Bank withdrawals',
                onTap: () => _openWithdrawalSheet(earnState.availableMinor),
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.bolt_rounded,
                accentColor: AppTheme.accentAmber,
                title: 'Promote / Feature Ad (پیڈ ایڈ)',
                subtitle: 'Pay to rank your advert in the top home banner',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const PaidAdPromotionScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.storefront_outlined,
                accentColor: AppTheme.primaryLavender,
                title: AppStrings.myListings,
                subtitle: 'Manage, pause, or mark your listings as sold',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const MyListingsScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.favorite_border_rounded,
                accentColor: AppTheme.accentPink,
                title: AppStrings.favorites,
                subtitle: 'View saved & bookmarked advertisements',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const FavoritesScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.verified_user_rounded,
                accentColor: AppTheme.secondaryMint,
                title: AppStrings.accountVerification,
                subtitle: 'Submit CNIC or Trade License for trust badge',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const VerificationScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.shield_rounded,
                accentColor: AppTheme.accentCyan,
                title: AppStrings.securitySessions,
                subtitle: 'Manage active devices and sessions',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const DevicesScreen()),
                  );
                },
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.language_rounded,
                accentColor: AppTheme.accentCyan,
                title: AppStrings.languageSetting,
                subtitle: lang == AppLanguage.ur ? 'اردو فعال ہے (تبدیل کریں)' : 'English active (Switch to Urdu)',
                onTap: () => ref.read(languageProvider.notifier).toggleLanguage(),
              ),
              const Divider(color: AppTheme.borderHairline),
              _buildMenuRow(
                icon: Icons.logout_rounded,
                accentColor: AppTheme.accentPink,
                title: AppStrings.signOut,
                subtitle: 'Disconnect this mobile device session',
                isDestructive: true,
                onTap: () async {
                  await ref.read(authProvider.notifier).logout();
                },
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildMenuRow({
    required IconData icon,
    required Color accentColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
    bool isDestructive = false,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(AppTheme.radiusSm),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 12),
        child: Row(
          children: [
            TintedIconBubble(
              icon: icon,
              tint: accentColor,
              size: 40,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: AppTheme.bodyMd.copyWith(
                      fontWeight: FontWeight.w700,
                      color: isDestructive ? AppTheme.accentPink : AppTheme.onSurface,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    subtitle,
                    style: AppTheme.caption.copyWith(
                      color: AppTheme.onSurfaceMuted,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(
              Icons.chevron_right_rounded,
              color: AppTheme.onSurfaceMuted,
              size: 20,
            ),
          ],
        ),
      ),
    );
  }
}
