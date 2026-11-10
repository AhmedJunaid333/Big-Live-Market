import 'package:flutter_test/flutter_test.dart';
import 'package:big_live_mobile/features/auth/models/user.dart';
import 'package:big_live_mobile/features/auth/providers/auth_provider.dart';

void main() {
  group('Auth Model & State Tests', () {
    test('User correctly parses from JSON', () {
      final json = {
        'id': 1,
        'uuid': 'd3b07384-d113-46d4-a745-f0b09f1e4a39',
        'name': 'Hamza Ahmed',
        'email': 'hamza@example.com',
        'phone_e164': '+923001234567',
        'status': 'active',
        'locale': 'en',
        'preferred_currency': 'PKR',
        'roles': [
          {'name': 'seller'}
        ],
      };

      final user = User.fromJson(json);

      expect(user.id, 1);
      expect(user.name, 'Hamza Ahmed');
      expect(user.roles, ['seller']);
      expect(user.preferredCurrency, 'PKR');
    });

    test('AuthState defaults to unauthenticated', () {
      const state = AuthState();
      expect(state.isAuthenticated, false);
      expect(state.isLoading, false);
      expect(state.user, null);
    });
  });
}
