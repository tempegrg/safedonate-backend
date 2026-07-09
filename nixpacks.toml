import 'package:flutter/material.dart';
import '../../services/verification_service.dart';
import 'scan_qr_page.dart';
import '../../config/app_theme.dart';

class VerifyLinkPage extends StatefulWidget {
  const VerifyLinkPage({super.key});

  @override
 State<VerifyLinkPage> createState() => _VerifyLinkPageState();
}

class _VerifyLinkPageState extends State<VerifyLinkPage> {
  // URL Controller
  final urlController = TextEditingController();

  // API Results
  String result = "";
  String organisationName = "";
  String status = "";
  String securityStatus = "";

  // Loading
  bool isLoading = false;

  // =========================================
  // VERIFY FUNCTION
  // =========================================
  void verifyLink() async {
    if (urlController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Please enter website URL"),
        ),
      );
      return;
    }

    setState(() {
      isLoading = true;
      result = "";
      organisationName = "";
      status = "";
      securityStatus = "";
    });

    var response = await VerificationService.verify(
      urlController.text.trim(),
    );

    setState(() {
      isLoading = false;
    });

    if (response != null) {
      setState(() {
        result = response['message'] ?? "";
        status = response['status'] ?? "";
        securityStatus = response['security_status'] ?? "";
        organisationName = response['organisation'] ?? "";
      });
    } else {
      setState(() {
        result = "Error checking link";
        status = "error";
        securityStatus = "unknown";
      });
    }
  }

  // =========================================
  // SCAN QR CODE
  // =========================================
  Future<void> scanQRCode() async {
    await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const ScanQrPage(),
      ),
    );
  }

  // =========================================
  // SECURITY DESCRIPTION
  // =========================================
  String getSecurityDescription(String securityStatus) {
    switch (securityStatus.toLowerCase()) {
      case "safe":
        return "No major threats were detected from the website link during the security scan.";
      case "warning":
        return "Some security concerns were detected. Please review the website carefully before proceeding.";
      case "danger":
        return "Potential malicious or suspicious activity was detected for this website link. Do not proceed until it is verified.";
      default:
        return "The website security scan could not be completed at this time, or no scan result is available.";
    }
  }

  // =========================================
  // DISPLAY LABEL FOR STATUS
  // =========================================
  String getStatusTitle(String status) {
    switch (status.toLowerCase()) {
      case "verified":
        return "VERIFIED";
      case "warning":
        return "WARNING";
      case "danger":
        return "DANGEROUS";
      case "unknown":
        return "UNKNOWN";
      case "error":
        return "ERROR";
      default:
        return status.toUpperCase();
    }
  }

  @override
  Widget build(BuildContext context) {
    // =========================================
    // CARD COLORS
    // =========================================
    Color cardColor = const Color(0xFFFDF2F8); // unknown bg
    Color borderColor = const Color(0xFF800020); // maroon
    Color textColor = const Color(0xFF800020);
    IconData resultIcon = Icons.help_outline_rounded;

    if (status == "verified") {
      cardColor = const Color(0xFFF0FDF4);
      borderColor = const Color(0xFF16A34A);
      textColor = const Color(0xFF166534);
      resultIcon = Icons.check_circle;
    } else if (status == "warning") {
      cardColor = const Color(0xFFFFFBEB);
      borderColor = const Color(0xFFD97706);
      textColor = const Color(0xFF92400E);
      resultIcon = Icons.warning_amber_rounded;
    } else if (status == "danger") {
      cardColor = const Color(0xFFFEF2F2);
      borderColor = const Color(0xFFDC2626);
      textColor = const Color(0xFF991B1B);
      resultIcon = Icons.dangerous;
    } else if (status == "unknown") {
      cardColor = const Color(0xFFF1F5F9);
      borderColor = const Color(0xFF64748B);
      textColor = const Color(0xFF334155);
      resultIcon = Icons.help_outline_rounded;
    } else if (status == "error") {
      cardColor = const Color(0xFFF8FAFC);
      borderColor = const Color(0xFF64748B);
      textColor = const Color(0xFF334155);
      resultIcon = Icons.error_outline;
    }

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,

      appBar: AppBar(
        title: const Text(
          "Verify Donation Link",
          style: TextStyle(color: Colors.white),
        ),
        centerTitle: true,
        backgroundColor: AppTheme.primaryColor,
      ),

      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),

        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 20),

            // =========================================
            // PAGE TITLE
            // =========================================
            const Text(
              "Donation Link Verification",
              style: TextStyle(
                fontSize: 26,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            const Text(
              "Check whether a donation website is trusted and verified.",
              style: TextStyle(
                color: Colors.black54,
                fontSize: 15,
              ),
            ),

            const SizedBox(height: 30),

            // =========================================
            // URL INPUT
            // =========================================
            TextField(
              controller: urlController,
              decoration: InputDecoration(
                hintText: "Enter website URL",
                prefixIcon: const Icon(
                  Icons.link,
                  color: AppTheme.primaryColor,
                ),
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide.none,
                ),
              ),
            ),

            const SizedBox(height: 24),

            // =========================================
            // VERIFY BUTTON
            // =========================================
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: isLoading ? null : verifyLink,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryColor,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                child: isLoading
                    ? const CircularProgressIndicator(
                        color: Colors.white,
                      )
                    : const Text(
                        "Verify Link",
                        style: TextStyle(
                          fontSize: 18,
                          color: Colors.white,
                        ),
                      ),
              ),
            ),

            const SizedBox(height: 12),

            // =========================================
            // SCAN QR BUTTON
            // =========================================
            SizedBox(
              width: double.infinity,
              height: 55,
              child: OutlinedButton.icon(
                icon: const Icon(Icons.qr_code_scanner),
                label: const Text(
                  "Scan QR Code",
                  style: TextStyle(fontSize: 16),
                ),
                onPressed: scanQRCode,
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppTheme.primaryColor,
                  side: const BorderSide(color: AppTheme.primaryColor),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
              ),
            ),

            const SizedBox(height: 30),

            // =========================================
            // RESULT CARD
            // =========================================
            if (result.isNotEmpty)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: cardColor,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: borderColor,
                    width: 2,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: borderColor.withOpacity(0.12),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),

                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // =========================================
                    // STATUS HEADER
                    // =========================================
                    Row(
                      children: [
                        Icon(
                          resultIcon,
                          color: borderColor,
                          size: 30,
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            getStatusTitle(status),
                            style: TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.bold,
                              color: textColor,
                            ),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 18),

                    // =========================================
                    // MESSAGE
                    // =========================================
                    Text(
                      result,
                      style: const TextStyle(
                        fontSize: 16,
                        height: 1.5,
                      ),
                    ),

                    // =========================================
                    // ORGANISATION NAME
                    // =========================================
                    if (organisationName.isNotEmpty) ...[
                      const SizedBox(height: 18),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Icon(
                            Icons.account_balance,
                            color: AppTheme.primaryColor,
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              organisationName,
                              style: const TextStyle(
                                fontSize: 17,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],

                    const SizedBox(height: 22),

                    // =========================================
                    // SECURITY STATUS SECTION
                    // =========================================
                    const Text(
                      "Security Status",
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),

                    const SizedBox(height: 10),

                    Row(
                      children: [
                        const Icon(
                          Icons.security,
                          color: Colors.black54,
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            securityStatus.isEmpty
                                ? "UNKNOWN"
                                : securityStatus.toUpperCase(),
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 10),

                    Text(
                      getSecurityDescription(securityStatus),
                      style: const TextStyle(
                        fontSize: 14,
                        color: Colors.black87,
                        height: 1.5,
                      ),
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }
}