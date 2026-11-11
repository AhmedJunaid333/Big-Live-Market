import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:big_live_mobile/main.dart';
import 'package:big_live_mobile/features/auth/screens/register_screen.dart';
import 'package:big_live_mobile/features/listings/providers/favorites_provider.dart';
import 'package:big_live_mobile/features/listings/providers/filter_provider.dart';
import 'package:big_live_mobile/features/listings/screens/favorites_screen.dart';
import 'package:big_live_mobile/features/profile/screens/devices_screen.dart';
import 'package:big_live_mobile/features/profile/screens/verification_screen.dart';

void main() {
  group('New Feature Suites Tests', () {
    test('FavoritesNotifier toggles items correctly', () async {
      final container = ProviderContainer();
      final notifier = container.read(favoritesProvider.notifier);

      expect(container.read(favoritesProvider).contains(42), isFalse);

      await notifier.toggleFavorite(42);
      expect(container.read(favoritesProvider).contains(42), isTrue);

      await notifier.toggleFavorite(42);
      expect(container.read(favoritesProvider).contains(42), isFalse);
    });

    test('FilterNotifier updates filters and sorts', () {
      final container = ProviderContainer();
      final notifier = container.read(filterProvider.notifier);

      expect(container.read(filterProvider).isActive, isFalse);

      notifier.setCondition('new');
      expect(container.read(filterProvider).condition, 'new');
      expect(container.read(filterProvider).isActive, isTrue);

      notifier.setCity('Lahore');
      expect(container.read(filterProvider).city, 'Lahore');

      notifier.setSortBy('price_asc');
      expect(container.read(filterProvider).sortBy, 'price_asc');

      notifier.reset();
      expect(container.read(filterProvider).isActive, isFalse);
      expect(container.read(filterProvider).condition, isNull);
    });

    testWidgets('RegisterScreen renders form and role selector', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(
            home: RegisterScreen(),
          ),
        ),
      );

      expect(find.text('CREATE ACCOUNT'), findsNWidgets(2)); // Title + Button
      expect(find.text('Buy Items'), findsOneWidget);
      expect(find.text('Sell Items'), findsOneWidget);
      expect(find.text('Full Name'), findsOneWidget);
      expect(find.text('Email Address'), findsOneWidget);
      expect(find.text('Password'), findsOneWidget);
    });

    testWidgets('FavoritesScreen displays empty state when no bookmarks', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            listingsFeedProvider.overrideWith((ref) => Future.value([])),
          ],
          child: const MaterialApp(
            home: FavoritesScreen(),
          ),
        ),
      );
      await tester.pumpAndSettle();

      expect(find.text('SAVED ITEMS'), findsOneWidget);
      expect(find.text('0 Saved'), findsOneWidget);
      expect(find.text('No saved listings yet'), findsOneWidget);
    });

    testWidgets('VerificationScreen renders document options and inputs', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(
            home: VerificationScreen(),
          ),
        ),
      );

      expect(find.text('Account Verification'), findsOneWidget);
      expect(find.text('CNIC'), findsOneWidget);
      expect(find.text('Passport'), findsOneWidget);
      expect(find.text('Trade License'), findsOneWidget);
      expect(find.text('SUBMIT FOR VERIFICATION'), findsOneWidget);
    });
  });
}
