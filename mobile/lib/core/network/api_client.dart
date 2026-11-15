import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  static const String defaultBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://market.biglive.online/api/v1',
  );

  static const String fallbackBaseUrl = 'http://127.0.0.1:8000/api/v1';

  final Dio _dio;
  final FlutterSecureStorage _storage;

  ApiClient({Dio? dio, FlutterSecureStorage? storage})
      : _dio = dio ?? Dio(BaseOptions(
          baseUrl: defaultBaseUrl,
          connectTimeout: const Duration(seconds: 15),
          receiveTimeout: const Duration(seconds: 15),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        )),
        _storage = storage ?? const FlutterSecureStorage() {
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _storage.read(key: 'auth_token');
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException error, handler) async {
          // If connection fails on 127.0.0.1, auto-retry with Wi-Fi LAN IP
          if ((error.type == DioExceptionType.connectionTimeout ||
                  error.type == DioExceptionType.connectionError) &&
              error.requestOptions.extra['retried_alternate'] != true) {
            final options = error.requestOptions;
            options.extra['retried_alternate'] = true;
            
            final isLocalhost = options.baseUrl.contains('127.0.0.1') || options.baseUrl.contains('localhost');
            options.baseUrl = isLocalhost ? fallbackBaseUrl : defaultBaseUrl;

            try {
              final response = await _dio.fetch(options);
              return handler.resolve(response);
            } catch (e) {
              // Alternate failed too, return original error
            }
          }
          return handler.next(error);
        },
      ),
    );
  }

  Dio get dio => _dio;
  FlutterSecureStorage get storage => _storage;
}
