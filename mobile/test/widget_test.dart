import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:big_live_mobile/main.dart';

void main() {
  testWidgets('BigLiveApp boots and displays main navigation tabs', (WidgetTester tester) async {
    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          listingsFeedProvider.overrideWith((ref) async => []),
        ],
        child: const BigLiveApp(),
      ),
    );

    await tester.pumpAndSettle();

    expect(find.text('Big Live Marketplace'), findsOneWidget);
    expect(find.text('Home'), findsOneWidget);
    expect(find.text('Categories'), findsOneWidget);
    expect(find.text('Sell'), findsOneWidget);
    expect(find.text('Chats'), findsOneWidget);
    expect(find.text('Profile'), findsOneWidget);
    expect(find.text('No active listings found'), findsOneWidget);
  });
}
