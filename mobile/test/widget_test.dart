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

    // Allow splash timer to complete or pump and settle
    await tester.pump(const Duration(milliseconds: 3000));
    await tester.pumpAndSettle();

    expect(find.text('Big Live'), findsOneWidget);
    expect(find.text('NEBULA MARKETPLACE'), findsOneWidget);
    expect(find.byType(FloatingGlassTabBar), findsOneWidget);
    expect(find.text('No active listings found'), findsOneWidget);
  });
}
