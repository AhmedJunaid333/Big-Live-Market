import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/network/api_client.dart';
import '../models/user.dart';

class AuthState {
  final bool isAuthenticated;
  final bool isLoading;
  final User? user;
  final String? errorMessage;

  const AuthState({
    this.isAuthenticated = false,
    this.isLoading = false,
    this.user,
    this.errorMessage,
  });

  AuthState copyWith({
    bool? isAuthenticated,
    bool? isLoading,
    User? user,
    String? errorMessage,
  }) {
    return AuthState(
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      isLoading: isLoading ?? this.isLoading,
      user: user ?? this.user,
      errorMessage: errorMessage,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final ApiClient _apiClient;

  AuthNotifier(this._apiClient) : super(const AuthState()) {
    checkAuthStatus();
  }

  Future<void> checkAuthStatus() async {
    final token = await _apiClient.storage.read(key: 'auth_token');
    if (token == null) {
      state = state.copyWith(isAuthenticated: false);
      return;
    }

    try {
      final response = await _apiClient.dio.get('/me');
      final userData = response.data['data'] as Map<String, dynamic>;
      final localAvatar = await _apiClient.storage.read(key: 'avatar_path_${userData['id']}');
      state = state.copyWith(
        isAuthenticated: true,
        user: User.fromJson(userData, localAvatarPath: localAvatar),
      );
    } catch (_) {
      await _apiClient.storage.delete(key: 'auth_token');
      state = state.copyWith(isAuthenticated: false, user: null);
    }
  }

  Future<bool> login(String email, String password, {String? deviceName}) async {
    state = state.copyWith(isLoading: true, errorMessage: null);

    try {
      final response = await _apiClient.dio.post('/auth/login', data: {
        'email': email,
        'password': password,
        'device_name': deviceName ?? 'Flutter Mobile App',
      });

      final data = response.data['data'] as Map<String, dynamic>;
      final token = data['token'] as String;
      final user = User.fromJson(data['user'] as Map<String, dynamic>);

      await _apiClient.storage.write(key: 'auth_token', value: token);

      state = state.copyWith(
        isLoading: false,
        isAuthenticated: true,
        user: user,
      );
      return true;
    } on DioException catch (e) {
      String message;
      if (e.response != null && e.response?.data is Map) {
        message = e.response?.data['message']?.toString() ?? 'Invalid email or password.';
      } else if (e.type == DioExceptionType.connectionError || e.type == DioExceptionType.connectionTimeout) {
        message = 'Cannot reach backend server. Check Wi-Fi or USB ADB reverse.';
      } else {
        message = e.message ?? 'Login failed. Please check credentials.';
      }
      state = state.copyWith(isLoading: false, errorMessage: message);
      return false;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    String? phoneE164,
    String? role,
  }) async {
    state = state.copyWith(isLoading: true, errorMessage: null);

    try {
      final response = await _apiClient.dio.post('/auth/register', data: {
        'name': name,
        'email': email,
        'password': password,
        'phone_e164': phoneE164,
        'role': role ?? 'buyer',
        'device_name': 'Flutter Mobile App',
      });

      final data = response.data['data'] as Map<String, dynamic>;
      final token = data['token'] as String;
      final user = User.fromJson(data['user'] as Map<String, dynamic>);

      await _apiClient.storage.write(key: 'auth_token', value: token);

      state = state.copyWith(
        isLoading: false,
        isAuthenticated: true,
        user: user,
      );
      return true;
    } on DioException catch (e) {
      final message = e.response?.data['message']?.toString() ?? 'Registration failed.';
      state = state.copyWith(isLoading: false, errorMessage: message);
      return false;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _apiClient.dio.post('/auth/logout');
    } catch (_) {}

    await _apiClient.storage.delete(key: 'auth_token');
    state = const AuthState();
  }

  Future<bool> updateProfile({
    required String name,
    String? phone,
    String? avatarPath,
  }) async {
    if (state.user == null) return false;
    state = state.copyWith(isLoading: true, errorMessage: null);

    try {
      if (avatarPath != null) {
        await _apiClient.storage.write(
          key: 'avatar_path_${state.user!.id}',
          value: avatarPath,
        );
      }

      try {
        final response = await _apiClient.dio.patch('/me', data: {
          'name': name,
          if (phone != null && phone.isNotEmpty) 'phone_e164': phone,
        });

        if (response.data is Map && response.data['data'] != null) {
          final updatedData = response.data['data'] as Map<String, dynamic>;
          final updatedUser = User.fromJson(
            updatedData,
            localAvatarPath: avatarPath ?? state.user!.avatarPath,
          );
          state = state.copyWith(isLoading: false, user: updatedUser);
          return true;
        }
      } catch (_) {
        // Fallback for offline or local changes
      }

      final updatedUser = state.user!.copyWith(
        name: name,
        phoneE164: phone ?? state.user!.phoneE164,
        avatarPath: avatarPath ?? state.user!.avatarPath,
      );
      state = state.copyWith(isLoading: false, user: updatedUser);
      return true;
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
      return false;
    }
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final client = ApiClient();
  return AuthNotifier(client);
});
