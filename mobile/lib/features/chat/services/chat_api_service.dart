import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class ChatApiService {
  final ApiClient _client;
  ChatApiService([ApiClient? client]) : _client = client ?? ApiClient();

  Future<List<dynamic>> getConversations() async {
    try {
      final response = await _client.dio.get('/conversations');
      final data = response.data['data'];
      if (data is List) return data;
      return [];
    } catch (_) {
      return [];
    }
  }

  Future<Map<String, dynamic>> startConversation({
    required int listingId,
    String? initialMessage,
  }) async {
    try {
      final response = await _client.dio.post(
        '/conversations',
        data: {
          'listing_id': listingId,
          if (initialMessage != null && initialMessage.isNotEmpty)
            'initial_message': initialMessage,
        },
      );
      return (response.data['data'] as Map<String, dynamic>?) ?? {};
    } on DioException catch (e) {
      final msg = e.response?.data['message'] ?? e.message ?? 'Failed to start conversation';
      throw Exception(msg);
    }
  }

  Future<List<dynamic>> getMessages(int conversationId) async {
    try {
      final response = await _client.dio.get('/conversations/$conversationId/messages');
      final data = response.data['data'];
      if (data is List) return data;
      return [];
    } catch (_) {
      return [];
    }
  }

  Future<Map<String, dynamic>> sendMessage({
    required int conversationId,
    required String body,
    int? mediaId,
  }) async {
    try {
      final response = await _client.dio.post(
        '/conversations/$conversationId/messages',
        data: {
          'body': body,
          if (mediaId != null) 'media_id': mediaId,
        },
      );
      return (response.data['data'] as Map<String, dynamic>?) ?? {};
    } on DioException catch (e) {
      final msg = e.response?.data['message'] ?? e.message ?? 'Failed to send message';
      throw Exception(msg);
    }
  }

  Future<void> markAsRead(int conversationId) async {
    try {
      await _client.dio.post('/conversations/$conversationId/read');
    } catch (_) {}
  }

  Future<Map<String, dynamic>> sendOffer({
    required int conversationId,
    required int amountMinor,
  }) async {
    try {
      final response = await _client.dio.post(
        '/conversations/$conversationId/offers',
        data: {'amount_minor': amountMinor},
      );
      return (response.data['data'] as Map<String, dynamic>?) ?? {};
    } on DioException catch (e) {
      final msg = e.response?.data['message'] ?? e.message ?? 'Failed to send offer';
      throw Exception(msg);
    }
  }
}
