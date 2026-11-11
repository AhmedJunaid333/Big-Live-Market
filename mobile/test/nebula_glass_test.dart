import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:big_live_mobile/core/theme/app_theme.dart';
import 'package:big_live_mobile/core/widgets/nebula_glass_widgets.dart';
import 'package:big_live_mobile/features/categories/screens/categories_screen.dart';
import 'package:big_live_mobile/features/categories/screens/category_listings_screen.dart';
import 'package:big_live_mobile/features/profile/screens/profile_screen.dart';
import 'package:big_live_mobile/features/listings/screens/listing_detail_screen.dart';
import 'package:big_live_mobile/features/listings/screens/post_ad_screen.dart';
import 'package:big_live_mobile/features/auth/screens/login_screen.dart';

void main() {
  group('Nebula Glass Mobile Design System Tokens', () {
    test('AppTheme defines exact Nebula Glass palette', () {
      expect(AppTheme.background, const Color(0xFF0F0C29));
      expect(AppTheme.backgroundMid, const Color(0xFF1A103C));
      expect(AppTheme.backgroundDeep, const Color(0xFF2E1065));
      expect(AppTheme.surface, const Color(0xFF17153B));
      expect(AppTheme.surfaceGlass, const Color(0x0DFFFFFF)); // rgba(255,255,255,0.05)
      expect(AppTheme.borderHairline, const Color(0x1AFFFFFF)); // rgba(255,255,255,0.10)
      expect(AppTheme.primaryLavender, const Color(0xFFD8B4FE));
      expect(AppTheme.onPrimary, const Color(0xFF1A103C));
      expect(AppTheme.secondaryMint, const Color(0xFF99F6E4));
      expect(AppTheme.accentPink, const Color(0xFFF9A8D4));
      expect(AppTheme.accentMint, const Color(0xFF6EE7B7));
      expect(AppTheme.accentBlue, const Color(0xFF93C5FD));
      expect(AppTheme.accentIndigo, const Color(0xFF818CF8));
      expect(AppTheme.accentRose, const Color(0xFFF87171));
      expect(AppTheme.accentAmber, const Color(0xFFFCD34D));
    });

    test('AppTheme defines exact Nebula Glass radii and spacing', () {
      expect(AppTheme.radiusSm, 12.0);
      expect(AppTheme.radiusMd, 16.0);
      expect(AppTheme.radiusLg, 22.0);
      expect(AppTheme.radiusXl, 24.0);
      expect(AppTheme.radius2Xl, 32.0);
      expect(AppTheme.radiusFull, 9999.0);

      expect(AppTheme.gutter, 24.0);
      expect(AppTheme.cardPadding, 24.0);
      expect(AppTheme.cardPaddingSm, 20.0);
      expect(AppTheme.sectionGap, 24.0);
    });

    testWidgets('NebulaGlassCard renders with glass styling and backdrop blur', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: NebulaAtmosphericBackground(
              child: NebulaGlassCard(
                useBlur: true,
                child: const Text('Glass Content', style: TextStyle(color: Colors.white)),
              ),
            ),
          ),
        ),
      );

      expect(find.text('Glass Content'), findsOneWidget);
      expect(find.byType(BackdropFilter), findsWidgets);
    });

    testWidgets('NebulaButtonPrimary renders with gradient and uppercase typography', (tester) async {
      bool tapped = false;
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: NebulaButtonPrimary(
              label: 'Publish Advert',
              onPressed: () => tapped = true,
            ),
          ),
        ),
      );

      expect(find.text('PUBLISH ADVERT'), findsOneWidget);
      await tester.tap(find.text('PUBLISH ADVERT'));
      expect(tapped, isTrue);
    });

    testWidgets('CategoriesScreen renders glass cards and departments', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            dynamicCategoriesProvider.overrideWith((ref) async => CategoriesScreen.defaultCategories),
          ],
          child: const MaterialApp(
            home: CategoriesScreen(),
          ),
        ),
      );
      await tester.pumpAndSettle();

      expect(find.text('Categories'), findsOneWidget);
      expect(find.text('Motors & Cars'), findsOneWidget);
      expect(find.text('Mobile Phones'), findsOneWidget);
      expect(find.byType(NebulaGlassCard), findsWidgets);
    });

    testWidgets('ProfileScreen renders guest card in Nebula Glass', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(
            home: ProfileScreen(),
          ),
        ),
      );

      expect(find.text('Profile'), findsOneWidget);
      expect(find.text('Log in to your account'), findsOneWidget);
      expect(find.text('SIGN IN / REGISTER'), findsOneWidget);
    });

    testWidgets('ListingDetailScreen renders glass cards and actions', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          child: MaterialApp(
            home: ListingDetailScreen(
              listing: {
                'title': 'Test Luxury Sedan',
                'price_minor': 500000000,
                'currency': 'PKR',
                'condition_code': 'new',
                'location_label': 'Lahore, Pakistan',
                'description': 'Pristine showroom condition.',
                'seller': {'name': 'Shahid Khan', 'phone_e164': '+923001112233'}
              },
            ),
          ),
        ),
      );

      expect(find.text('Test Luxury Sedan'), findsWidgets);
      expect(find.text('PKR 5000000'), findsOneWidget);
      expect(find.text('Shahid Khan'), findsOneWidget);
      expect(find.text('DIRECT CHAT'), findsOneWidget);
    });

    testWidgets('PostAdScreen renders glass form and submit button', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(
            home: PostAdScreen(),
          ),
        ),
      );

      expect(find.text('Post an Advert'), findsOneWidget);
      expect(find.text('ITEM TITLE'), findsOneWidget);
      expect(find.text('PUBLISH ADVERT'), findsOneWidget);
    });

    testWidgets('LoginScreen renders glass sign in form and demo roles', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(
            home: LoginScreen(),
          ),
        ),
      );

      expect(find.text('Welcome Back'), findsOneWidget);
      expect(find.text('EMAIL ADDRESS'), findsOneWidget);
      expect(find.text('SIGN IN'), findsOneWidget);
      expect(find.text('Admin'), findsOneWidget);
      expect(find.text('Seller'), findsOneWidget);
    });

    testWidgets('CategoryListingsScreen renders department header and items', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            categoryListingsProvider(1).overrideWith((ref) async => [
              {
                'id': 1,
                'title': 'Honda Civic RS Turbo',
                'price_minor': 850000000,
                'currency': 'PKR',
                'condition_code': 'used',
                'location_label': 'Lahore, Pakistan',
              }
            ]),
          ],
          child: const MaterialApp(
            home: CategoryListingsScreen(
              categoryId: 1,
              categoryName: 'Motors & Cars',
              icon: Icons.directions_car_rounded,
              accentColor: AppTheme.accentPink,
            ),
          ),
        ),
      );
      await tester.pumpAndSettle();

      expect(find.text('Motors & Cars'), findsOneWidget);
      expect(find.text('DEPARTMENT'), findsOneWidget);
      expect(find.text('Honda Civic RS Turbo'), findsOneWidget);
      expect(find.byType(NebulaGlassCard), findsWidgets);
    });
  });
}
