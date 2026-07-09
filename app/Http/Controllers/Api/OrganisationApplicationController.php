import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

import '../../config/app_theme.dart';
import '../../services/organisation_application_admin_service.dart';

class OrganisationApplicationDetailPage extends StatefulWidget {
  final int applicationId;

  const OrganisationApplicationDetailPage({
    super.key,
    required this.applicationId,
  });

  @override
  State<OrganisationApplicationDetailPage> createState() =>
      _OrganisationApplicationDetailPageState();
}

class _OrganisationApplicationDetailPageState
    extends State<OrganisationApplicationDetailPage> {
  bool isLoading = true;
  Map<String, dynamic>? application;

  @override
  void initState() {
    super.initState();
    loadApplication();
  }

  Future<void> loadApplication() async {
    final result =
        await OrganisationApplicationAdminService.getApplication(
      widget.applicationId,
    );

    if (!mounted) return;

    setState(() {
      application = result;
      isLoading = false;
    });
  }

  // =========================================
  // APPROVE
  // =========================================
  Future<void> approve() async {
    final success =
        await OrganisationApplicationAdminService.approve(
      widget.applicationId,
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Organisation approved successfully"),
        ),
      );
      Navigator.pop(context, true);
    }
  }

  // =========================================
  // REJECT
  // =========================================
  Future<void> reject() async {
    final reasonController = TextEditingController();

    final result = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text("Reject Application"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text(
                "Add rejection reason for the applicant (optional).",
              ),
              const SizedBox(height: 12),
              TextField(
                controller: reasonController,
                maxLines: 4,
                decoration: InputDecoration(
                  hintText: "Enter rejection reason",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text("Cancel"),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(context, true),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                foregroundColor: Colors.white,
              ),
              child: const Text("Reject"),
            ),
          ],
        );
      },
    );

    if (result != true) return;

    final success =
        await OrganisationApplicationAdminService.reject(
      widget.applicationId,
      adminRemark: reasonController.text.trim().isEmpty
          ? null
          : reasonController.text.trim(),
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Application rejected"),
        ),
      );
      Navigator.pop(context, true);
    }
  }

  // =========================================
  // DELETE
  // =========================================
  Future<void> deleteApplication() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text("Delete Application"),
        content: const Text(
          "Are you sure you want to delete this application?",
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text("Cancel"),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
            child: const Text("Delete"),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    final success =
        await OrganisationApplicationAdminService.deleteApplication(
      widget.applicationId,
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Application deleted successfully"),
        ),
      );
      Navigator.pop(context, true);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Failed to delete application"),
        ),
      );
    }
  }

  // =========================================
  // EDIT
  // =========================================
  Future<void> editApplication() async {
    if (application == null) return;

    final organisationNameController = TextEditingController(
      text: application!['organisation_name']?.toString() ?? '',
    );
    final organisationTypeController = TextEditingController(
      text: application!['organisation_type']?.toString() ?? '',
    );
    final registrationNumberController = TextEditingController(
      text: application!['registration_number']?.toString() ?? '',
    );
    final descriptionController = TextEditingController(
      text: application!['description']?.toString() ?? '',
    );
    final emailController = TextEditingController(
      text: application!['email']?.toString() ?? '',
    );
    final phoneController = TextEditingController(
      text: application!['phone']?.toString() ?? '',
    );
    final addressController = TextEditingController(
      text: application!['address']?.toString() ?? '',
    );
    final websiteController = TextEditingController(
      text: application!['website']?.toString() ?? '',
    );

    final result = await showDialog<bool>(
      context: context,
      barrierDismissible: false,
      builder: (_) => AlertDialog(
        title: const Text("Edit Application"),
        content: SizedBox(
          width: double.maxFinite,
          child: SingleChildScrollView(
            child: Column(
              children: [
                buildDialogField(
                  controller: organisationNameController,
                  label: "Organisation Name",
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: organisationTypeController,
                  label: "Organisation Type",
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: registrationNumberController,
                  label: "Registration Number",
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: emailController,
                  label: "Email",
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: phoneController,
                  label: "Phone",
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: websiteController,
                  label: "Website",
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: addressController,
                  label: "Address",
                  maxLines: 3,
                ),
                const SizedBox(height: 12),
                buildDialogField(
                  controller: descriptionController,
                  label: "Description",
                  maxLines: 4,
                ),
              ],
            ),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text("Cancel"),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primaryColor,
              foregroundColor: Colors.white,
            ),
            child: const Text("Save"),
          ),
        ],
      ),
    );

    if (result != true) return;

    final success =
        await OrganisationApplicationAdminService.updateApplication(
      id: widget.applicationId,
      organisationName: organisationNameController.text.trim(),
      organisationType: organisationTypeController.text.trim(),
      registrationNumber: registrationNumberController.text.trim(),
      description: descriptionController.text.trim(),
      email: emailController.text.trim(),
      phone: phoneController.text.trim(),
      address: addressController.text.trim(),
      website: websiteController.text.trim(),
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Application updated successfully"),
        ),
      );
      await loadApplication();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Failed to update application"),
        ),
      );
    }
  }

  // =========================================
  // OPEN DOCUMENT INSIDE APP
  // =========================================
  void openDocumentInApp({
    required String title,
    required String url,
  }) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => InAppDocumentViewerPage(
          title: title,
          url: url,
        ),
      ),
    );
  }

  // =========================================
  // HELPERS
  // =========================================
  Widget buildDialogField({
    required TextEditingController controller,
    required String label,
    int maxLines = 1,
  }) {
    return TextField(
      controller: controller,
      maxLines: maxLines,
      decoration: InputDecoration(
        labelText: label,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
    );
  }

  Widget buildInfoTile(String title, dynamic value) {
    final displayValue =
        value?.toString().trim().isNotEmpty == true
            ? value.toString()
            : "-";

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFF9FAFC),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: Colors.grey.shade200,
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 125,
            child: Text(
              title,
              style: const TextStyle(
                fontSize: 13,
                color: Colors.black54,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              displayValue,
              style: const TextStyle(
                fontSize: 14.5,
                color: Colors.black87,
                fontWeight: FontWeight.w500,
                height: 1.4,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget buildSectionCard({
    required String title,
    required Widget child,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: const [
          BoxShadow(
            color: Colors.black12,
            blurRadius: 7,
            offset: Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }

  Widget buildDocumentTile({
    required IconData icon,
    required Color iconColor,
    required String title,
    required String? url,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFF9FAFC),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: Colors.grey.shade200,
        ),
      ),
      child: Row(
        children: [
          Icon(
            icon,
            color: iconColor,
            size: 24,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              title,
              style: const TextStyle(
                fontSize: 14.5,
                fontWeight: FontWeight.w600,
                color: Colors.black87,
              ),
            ),
          ),
          ElevatedButton(
            onPressed: url != null && url.trim().isNotEmpty
                ? () => openDocumentInApp(
                      title: title,
                      url: url,
                    )
                : null,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primaryColor,
              foregroundColor: Colors.white,
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            child: const Text("View"),
          ),
        ],
      ),
    );
  }

  Widget buildLogo(dynamic logoUrl) {
    if (logoUrl != null && logoUrl.toString().trim().isNotEmpty) {
      return CircleAvatar(
        radius: 52,
        backgroundColor: Colors.white,
        child: ClipOval(
          child: Image.network(
            logoUrl.toString(),
            width: 104,
            height: 104,
            fit: BoxFit.cover,
            errorBuilder: (context, error, stackTrace) {
              return Container(
                width: 104,
                height: 104,
                color: AppTheme.primaryColor.withOpacity(0.10),
                child: const Icon(
                  Icons.business,
                  size: 44,
                  color: AppTheme.primaryColor,
                ),
              );
            },
          ),
        ),
      );
    }

    return CircleAvatar(
      radius: 52,
      backgroundColor: AppTheme.primaryColor.withOpacity(0.10),
      child: const Icon(
        Icons.business,
        size: 44,
        color: AppTheme.primaryColor,
      ),
    );
  }

  Color getStatusBg(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return Colors.orange.shade100;
      case 'approved':
      case 'verified':
        return Colors.green.shade100;
      case 'rejected':
        return Colors.red.shade100;
      default:
        return Colors.grey.shade200;
    }
  }

  Color getStatusTextColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return Colors.orange;
      case 'approved':
      case 'verified':
        return Colors.green;
      case 'rejected':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  ButtonStyle actionButtonStyle({
    required Color backgroundColor,
  }) {
    return ElevatedButton.styleFrom(
      backgroundColor: backgroundColor,
      foregroundColor: Colors.white,
      minimumSize: const Size(0, 54),
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(14),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return const Scaffold(
        backgroundColor: Color(0xFFF5F7FA),
        body: Center(
          child: CircularProgressIndicator(),
        ),
      );
    }

    if (application == null) {
      return const Scaffold(
        backgroundColor: Color(0xFFF5F7FA),
        body: Center(
          child: Text("Application not found"),
        ),
      );
    }

    final status =
        (application!['status'] ?? 'pending').toString();
    final isPending = status.toLowerCase() == 'pending';

    final logoUrl = application!['logo_url'];
    final applicantName =
        application!['submitted_by_name'] ?? "Unknown";
    final applicantEmail =
        application!['submitted_by_email'] ?? "No email";
    final adminRemark = application!['admin_remark'];

    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        backgroundColor: AppTheme.primaryColor,
        elevation: 0,
        centerTitle: true,
        title: const Text(
          "Application Details",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
          overflow: TextOverflow.ellipsis,
        ),
        actions: [
          IconButton(
            onPressed: editApplication,
            icon: const Icon(
              Icons.edit,
              color: Colors.white,
            ),
          ),
          IconButton(
            onPressed: deleteApplication,
            icon: const Icon(
              Icons.delete_outline,
              color: Colors.white,
            ),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // HEADER CARD
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [
                    AppTheme.primaryColor,
                    Color(0xFF9A1F42),
                  ],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.primaryColor.withOpacity(0.22),
                    blurRadius: 14,
                    offset: const Offset(0, 6),
                  ),
                ],
              ),
              child: Column(
                children: [
                  buildLogo(logoUrl),
                  const SizedBox(height: 16),
                  Text(
                    (application!['organisation_name'] ?? "-")
                        .toString(),
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                      height: 1.25,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    (application!['organisation_type'] ?? "-")
                        .toString(),
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 15,
                    ),
                  ),
                  const SizedBox(height: 14),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 14,
                      vertical: 8,
                    ),
                    decoration: BoxDecoration(
                      color: getStatusBg(status),
                      borderRadius: BorderRadius.circular(30),
                    ),
                    child: Text(
                      status.toUpperCase(),
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        color: getStatusTextColor(status),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 18),

            buildSectionCard(
              title: "Applicant Information",
              child: Column(
                children: [
                  buildInfoTile("Submitted By", applicantName),
                  buildInfoTile("Applicant Email", applicantEmail),
                ],
              ),
            ),

            const SizedBox(height: 18),

            buildSectionCard(
              title: "Organisation Information",
              child: Column(
                children: [
                  buildInfoTile(
                    "Registration No",
                    application!['registration_number'],
                  ),
                  buildInfoTile("Email", application!['email']),
                  buildInfoTile("Phone", application!['phone']),
                  buildInfoTile("Website", application!['website']),
                  buildInfoTile("Address", application!['address']),
                ],
              ),
            ),

            const SizedBox(height: 18),

            buildSectionCard(
              title: "Organisation Description",
              child: Text(
                (application!['description'] ?? "-").toString(),
                style: const TextStyle(
                  fontSize: 15,
                  height: 1.6,
                  color: Colors.black87,
                ),
              ),
            ),

            const SizedBox(height: 18),

            buildSectionCard(
              title: "Uploaded Documents",
              child: Column(
                children: [
                  buildDocumentTile(
                    icon: Icons.picture_as_pdf,
                    iconColor: Colors.red,
                    title: "Registration Certificate",
                    url: application!['certificate_url']?.toString(),
                  ),
                  if (application!['supporting_document_url'] != null)
                    buildDocumentTile(
                      icon: Icons.description,
                      iconColor: AppTheme.primaryColor,
                      title: "Supporting Document",
                      url: application!['supporting_document_url']
                          ?.toString(),
                    ),
                ],
              ),
            ),

            if (status.toLowerCase() == 'rejected' &&
                adminRemark != null &&
                adminRemark.toString().trim().isNotEmpty) ...[
              const SizedBox(height: 18),
              buildSectionCard(
                title: "Rejection Reason",
                child: Text(
                  adminRemark.toString(),
                  style: const TextStyle(
                    fontSize: 15,
                    height: 1.6,
                    color: Colors.black87,
                  ),
                ),
              ),
            ],

            const SizedBox(height: 24),

            if (isPending)
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton(
                      onPressed: reject,
                      style: actionButtonStyle(
                        backgroundColor: Colors.red,
                      ),
                      child: const Text(
                        "Reject",
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: approve,
                      style: actionButtonStyle(
                        backgroundColor: Colors.green,
                      ),
                      child: const Text(
                        "Approve",
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
          ],
        ),
      ),
    );
  }
}

// =========================================
// IN-APP DOCUMENT VIEWER
// =========================================
class InAppDocumentViewerPage extends StatefulWidget {
  final String title;
  final String url;

  const InAppDocumentViewerPage({
    super.key,
    required this.title,
    required this.url,
  });

  @override
  State<InAppDocumentViewerPage> createState() =>
      _InAppDocumentViewerPageState();
}

class _InAppDocumentViewerPageState
    extends State<InAppDocumentViewerPage> {
  late final WebViewController controller;
  bool isPageLoading = true;

  @override
  void initState() {
    super.initState();

    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (_) {
            if (mounted) {
              setState(() {
                isPageLoading = true;
              });
            }
          },
          onPageFinished: (_) {
            if (mounted) {
              setState(() {
                isPageLoading = false;
              });
            }
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.url));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        backgroundColor: AppTheme.primaryColor,
        centerTitle: true,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: controller),
          if (isPageLoading)
            const Center(
              child: CircularProgressIndicator(),
            ),
        ],
      ),
    );
  }
}