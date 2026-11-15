import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:big_live_mobile/main.dart';
import 'package:big_live_mobile/core/widgets/nebula_glass_widgets.dart';

void main() {
  testWidgets('BigLiveApp boots and displays main navigation tabs and empty state in Nebula Glass', (WidgetTester tester) async {
    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          listingsFeedProvider.overrideWith((ref) async => []),
        ],
        child: const BigLiveApp(),
      ),
    );

    // Allow splash animation (4600ms) to complete and advance frame
    await tester.pump(const Duration(milliseconds: 5000));
    await tester.pump(const Duration(milliseconds: 1000));

    expect(find.text('Big Live'), findsWidgets);
    expect(find.text('Motors'), findsOneWidget);
    expect(find.text('Property'), findsWidgets);
    expect(find.byType(FloatingGlassTabBar), findsOneWidget);
    expect(find.text('SELL'), findsOneWidget);
    expect(find.text('My Ads'), findsOneWidget);
    expect(find.text('No active listings found'), findsOneWidget);
  });
}
