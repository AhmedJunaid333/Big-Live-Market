import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:big_live_mobile/core/theme/app_theme.dart';
import 'package:big_live_mobile/core/widgets/nebula_glass_widgets.dart';

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
  });
}
